<?php

namespace Modules\IPAL\Services;

use Illuminate\Support\Facades\DB;
use Modules\IPAL\Models\IpalUpload;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Models\IpalJaringanPipa;

/**
 * Service for importing GeoJSON features into the database.
 * Orchestrates parsing, coordinate transformation, and batch insertion.
 */
class GeoJsonImportService
{
    private CoordinateTransformService $coordinateService;
    private GeoJsonParserService $parserService;

    public function __construct(
        CoordinateTransformService $coordinateService,
        GeoJsonParserService $parserService
    ) {
        $this->coordinateService = $coordinateService;
        $this->parserService = $parserService;
    }

    /**
     * Import a GeoJSON FeatureCollection into the database.
     *
     * @param IpalUpload $upload The upload record to associate features with
     * @param array $geojsonData Parsed GeoJSON data
     * @return int Number of features imported
     * @throws \RuntimeException If import fails
     */
    public function import(IpalUpload $upload, array $geojsonData): int
    {
        $this->parserService->validate($geojsonData);

        $features = $this->parserService->extractFeatures($geojsonData);
        $expectedType = $this->parserService->getExpectedGeometryType($upload->tipe);

        $this->parserService->validateGeometryType($features, $expectedType);

        $crs = $this->parserService->extractCrs($geojsonData);
        $needsTransform = $this->needsCoordinateTransform($crs);

        $count = 0;
        $importMetadata = [];

        DB::beginTransaction();

        try {
            if ($upload->tipe === 'manhole') {
                $chunks = array_chunk($features, 100);
                foreach ($chunks as $chunk) {
                    $count += $this->importManholes($upload, $chunk, $needsTransform);
                }
            } else {
                $pipeResult = $this->importPipes($upload, $features, $needsTransform);
                $count = $pipeResult['count'];
                $importMetadata['pipe_identity'] = $pipeResult['identity_stats'];
            }

            $upload->update([
                'total_fitur' => $count,
                'status' => 'completed',
                'metadata' => array_merge($upload->metadata ?? [], [
                    'crs' => $crs,
                    'features_imported' => $count,
                ], $importMetadata),
            ]);

            IpalUpload::setLatestAsActive($upload->tipe);

            DB::commit();

            return $count;
        } catch (\Exception $e) {
            DB::rollBack();

            $upload->update([
                'status' => 'failed',
                'pesan_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Determine if coordinate transformation is needed based on CRS.
     *
     * @param string|null $crs CRS identifier from GeoJSON
     * @return bool True if transformation from UTM to WGS84 is needed
     */
    private function needsCoordinateTransform(?string $crs): bool
    {
        if ($crs === null) {
            return false;
        }

        return str_contains($crs, 'EPSG::32749') || str_contains($crs, 'EPSG:32749');
    }

    /**
     * Import manhole features into the database.
     *
     * @param IpalUpload $upload The upload record
     * @param array $features Array of GeoJSON features
     * @param bool $needsTransform Whether coordinates need EPSG transformation
     * @return int Number of manholes imported
     */
    private function importManholes(IpalUpload $upload, array $features, bool $needsTransform): int
    {
        $count = 0;
        $resolvedCodes = [];
        foreach ($features as $index => $feature) {
            $props = $feature['properties'] ?? [];
            $resolvedCodes[$index] = $this->resolveManholeCode($props);
        }

        $assetCodes = array_values(array_unique($resolvedCodes));
        $statusMap = IpalAssetStatus::query()
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
            ->whereIn('asset_code', $assetCodes)
            ->get(['asset_code', 'status'])
            ->keyBy('asset_code');

        $existingAssetMap = IpalManhole::query()
            ->whereIn('kode_manhole', $assetCodes)
            ->get(['id', 'kode_manhole', 'status'])
            ->keyBy('kode_manhole');

        $statusUpserts = [];
        $timestamp = now();

        foreach ($features as $index => $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry'];

            if ($needsTransform) {
                $geometry = $this->coordinateService->transformGeometry($geometry);
            }

            $longitude = $geometry['coordinates'][0];
            $latitude = $geometry['coordinates'][1];

            $kodeManhole = $resolvedCodes[$index];
            $incomingStatus = IpalAssetStatus::normalizeStatus($props['STATUS'] ?? ($props['status'] ?? 'baik'));
            $canonicalStatus = $statusMap->get($kodeManhole)?->status;
            $existingAssetStatus = $existingAssetMap->get($kodeManhole)?->status;
            $effectiveStatus = $this->resolveEffectiveStatus($canonicalStatus, $existingAssetStatus, $incomingStatus);

            $manhole = IpalManhole::updateOrCreate(
                ['kode_manhole' => $kodeManhole],
                [
                    'upload_id' => $upload->id,
                    'bentuk' => $props['BENTUK'] ?? null,
                    'dim_mh' => $this->numericOrNull($props['DIM_MH'] ?? null),
                    'panjang' => $this->numericOrNull($props['PANJANG'] ?? null),
                    'lebar' => $this->numericOrNull($props['LEBAR'] ?? null),
                    'kedalaman' => $this->numericOrNull($props['KEDALAMAN'] ?? null),
                    'material_mh' => $props['MATERIALMH'] ?? null,
                    'struktur_mh' => $props['STR_MH'] ?? null,
                    'kondisi_mh' => $props['KONDISI_MH'] ?? null,
                    'sedimen' => $this->numericOrNull($props['SEDIMEN'] ?? null),
                    'jarak_pipa' => $this->numericOrNull($props['JARAKPIPA'] ?? null),
                    'ukuran_pipa' => $this->numericOrNull($props['UKURANPIPA'] ?? null),
                    'material_pipa' => $props['MATERIAL_P'] ?? null,
                    'sekitar' => $props['SEKITAR'] ?? null,
                    'surveyor' => $props['SURVEYOR'] ?? null,
                    'desa' => $props['DESA'] ?? null,
                    'kecamatan' => $props['KECAMATAN'] ?? null,
                    'ketinggian' => $this->numericOrNull($props['KETINGGIAN'] ?? null),
                    'topografi' => $props['TOPOGRAFI'] ?? null,
                    'jenis_tanah' => $props['JENISTANAH'] ?? null,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'geometry' => $geometry,
                    'foto_1' => $props['FOTO_1'] ?? null,
                    'foto_2' => $props['FOTO_2'] ?? null,
                    'foto_3' => $props['FOTO_3'] ?? null,
                    'foto_4' => $props['FOTO_4'] ?? null,
                    'probabilitas' => $this->numericOrNull($props['Probabilit'] ?? null),
                    'dampak' => $this->numericOrNull($props['Dampak'] ?? null),
                    'tingkat_risiko' => $this->numericOrNull($props['Tingkat_Ri'] ?? null),
                    'risiko' => $props['Risiko'] ?? null,
                    'klasifikasi' => $props['Klasifikas'] ?? null,
                    'pengendali' => $props['Pengendali'] ?? null,
                    'sektor' => $this->intOrNull($props['Sektor'] ?? null),
                    'status' => $effectiveStatus,
                    'wilayah' => $props['KECAMATAN'] ?? null,
                ]
            );

            $statusUpserts[] = [
                'asset_type' => IpalAssetStatus::ASSET_TYPE_MANHOLE,
                'asset_code' => $kodeManhole,
                'asset_id' => $manhole->id,
                'status' => $effectiveStatus,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $count++;
        }

        if (!empty($statusUpserts)) {
            IpalAssetStatus::query()->upsert(
                $statusUpserts,
                ['asset_type', 'asset_code'],
                ['asset_id', 'status', 'updated_at']
            );
        }

        return $count;
    }

    /**
     * Import pipe features into the database.
     *
     * @param IpalUpload $upload The upload record
     * @param array $features Array of GeoJSON features
     * @param bool $needsTransform Whether coordinates need EPSG transformation
     * @return array{count:int,identity_stats:array<string,int>}
     */
    private function importPipes(IpalUpload $upload, array $features, bool $needsTransform): array
    {
        $count = 0;
        $identityStats = [
            'with_id_jalur' => 0,
            'without_id_jalur' => 0,
            'matched_existing_fingerprint' => 0,
            'generated_auto_code' => 0,
        ];
        $activeUploadId = IpalUpload::activeCompletedUploadId('pipe');
        $existingCodeByFingerprint = $this->existingPipeCodeByFingerprint($activeUploadId);
        $preparedFeatures = [];
        $assetCodes = [];

        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry'];

            if ($needsTransform) {
                $geometry = $this->coordinateService->transformGeometry($geometry);
            }

            $identity = $this->resolvePipeIdentity($props, $geometry, $existingCodeByFingerprint);
            $assetCodes[] = $identity['kode_pipa'];
            $preparedFeatures[] = [
                'properties' => $props,
                'geometry' => $geometry,
                'identity' => $identity,
            ];

            if ($identity['identity_source'] === 'id_jalur') {
                $identityStats['with_id_jalur']++;
                continue;
            }

            $identityStats['without_id_jalur']++;
            if ($identity['identity_source'] === 'auto_hash_matched') {
                $identityStats['matched_existing_fingerprint']++;
                continue;
            }

            $identityStats['generated_auto_code']++;
        }

        $assetCodes = array_values(array_unique($assetCodes));
        $statusMap = IpalAssetStatus::query()
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE)
            ->whereIn('asset_code', $assetCodes)
            ->get(['asset_code', 'status'])
            ->keyBy('asset_code');

        $existingAssetMap = IpalJaringanPipa::query()
            ->whereIn('kode_pipa', $assetCodes)
            ->get(['id', 'kode_pipa', 'status'])
            ->keyBy('kode_pipa');

        $statusUpserts = [];
        $timestamp = now();

        foreach ($preparedFeatures as $prepared) {
            $props = $prepared['properties'];
            $geometry = $prepared['geometry'];
            $identity = $prepared['identity'];
            $kodePipa = $identity['kode_pipa'];
            $idJalur = $identity['id_jalur'];
            $incomingStatus = IpalAssetStatus::normalizeStatus($props['STATUS'] ?? ($props['status'] ?? 'baik'));
            $canonicalStatus = $statusMap->get($kodePipa)?->status;
            $existingAssetStatus = $existingAssetMap->get($kodePipa)?->status;
            $effectiveStatus = $this->resolveEffectiveStatus($canonicalStatus, $existingAssetStatus, $incomingStatus);

            $pipe = IpalJaringanPipa::updateOrCreate(
                ['kode_pipa' => $kodePipa],
                [
                    'upload_id' => $upload->id,
                    'id_jalur' => $idJalur,
                    'pipe_dia' => $this->numericOrNull($props['PIPE_DIA'] ?? null),
                    'fungsi' => isset($props['FUNGSI']) ? ucfirst(strtolower($props['FUNGSI'])) : null,
                    'length_km' => $this->numericOrNull($props['LENGTH_KM'] ?? null),
                    'tahun' => $this->intOrNull($props['YEAR'] ?? null),
                    'source' => $props['SOURCE'] ?? null,
                    'material' => $props['MATERIAL'] ?? null,
                    'geometry' => $geometry,
                    'status' => $effectiveStatus,
                    'wilayah' => null,
                ]
            );

            $statusUpserts[] = [
                'asset_type' => IpalAssetStatus::ASSET_TYPE_PIPE,
                'asset_code' => $kodePipa,
                'asset_id' => $pipe->id,
                'status' => $effectiveStatus,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $count++;
        }

        if (!empty($statusUpserts)) {
            IpalAssetStatus::query()->upsert(
                $statusUpserts,
                ['asset_type', 'asset_code'],
                ['asset_id', 'status', 'updated_at']
            );
        }

        return [
            'count' => $count,
            'identity_stats' => $identityStats,
        ];
    }

    /**
     * Convert a value to float or return null.
     *
     * @param mixed $value The value to convert
     * @return float|null
     */
    private function numericOrNull($value): ?float
    {
        if ($value === null || $value === '' || $value === 'None') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Convert a value to integer or return null.
     *
     * @param mixed $value The value to convert
     * @return int|null
     */
    private function intOrNull($value): ?int
    {
        if ($value === null || $value === '' || $value === 'None') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function resolveEffectiveStatus(?string $canonicalStatus, ?string $existingAssetStatus, string $incomingStatus): string
    {
        if ($canonicalStatus !== null && trim((string) $canonicalStatus) !== '') {
            return IpalAssetStatus::normalizeStatus($canonicalStatus);
        }

        if ($existingAssetStatus !== null && trim((string) $existingAssetStatus) !== '') {
            return IpalAssetStatus::normalizeStatus($existingAssetStatus);
        }

        return IpalAssetStatus::normalizeStatus($incomingStatus);
    }

    private function resolveManholeCode(array $props): string
    {
        $raw = trim((string) ($props['NOMOR_MH'] ?? ''));

        return $raw !== '' ? $raw : ('MH-' . uniqid());
    }

    private function resolvePipeIdentity(array $props, array $geometry, array $existingCodeByFingerprint): array
    {
        $raw = trim((string) ($props['ID_JALUR'] ?? ''));

        if ($raw !== '') {
            return [
                'kode_pipa' => $raw,
                'id_jalur' => $raw,
                'identity_source' => 'id_jalur',
            ];
        }

        $fingerprint = $this->buildPipeGeometryFingerprint($geometry);
        $matchedKodePipa = $existingCodeByFingerprint[$fingerprint] ?? null;

        if ($matchedKodePipa !== null && trim((string) $matchedKodePipa) !== '') {
            return [
                'kode_pipa' => $matchedKodePipa,
                'id_jalur' => null,
                'identity_source' => 'auto_hash_matched',
            ];
        }

        return [
            'kode_pipa' => 'PIPE-AUTO-' . strtoupper(substr($fingerprint, 0, 16)),
            'id_jalur' => null,
            'identity_source' => 'auto_hash_generated',
        ];
    }

    private function existingPipeCodeByFingerprint(?int $activeUploadId): array
    {
        $query = IpalJaringanPipa::query()->select(['kode_pipa', 'geometry']);

        if ($activeUploadId !== null) {
            $query->where('upload_id', $activeUploadId);
        }

        $map = [];
        $query->orderBy('id')->chunk(500, function ($rows) use (&$map): void {
            foreach ($rows as $row) {
                $kodePipa = trim((string) $row->kode_pipa);
                if ($kodePipa === '' || !is_array($row->geometry)) {
                    continue;
                }

                $fingerprint = $this->buildPipeGeometryFingerprint($row->geometry);
                if (!array_key_exists($fingerprint, $map)) {
                    $map[$fingerprint] = $kodePipa;
                }
            }
        });

        return $map;
    }

    private function buildPipeGeometryFingerprint(array $geometry): string
    {
        $payload = [
            'type' => strtoupper((string) ($geometry['type'] ?? '')),
            'coordinates' => $this->normalizeGeometryCoordinates($geometry['coordinates'] ?? []),
        ];

        return sha1(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function normalizeGeometryCoordinates($coordinates)
    {
        if (!is_array($coordinates)) {
            if (is_numeric($coordinates)) {
                return round((float) $coordinates, 6);
            }

            return $coordinates;
        }

        return array_map(fn ($value) => $this->normalizeGeometryCoordinates($value), $coordinates);
    }
}
