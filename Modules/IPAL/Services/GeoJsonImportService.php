<?php

namespace Modules\IPAL\Services;

use Illuminate\Support\Facades\DB;
use Modules\IPAL\Models\IpalUpload;
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

        DB::beginTransaction();

        try {
            $chunks = array_chunk($features, 100);

            foreach ($chunks as $chunk) {
                if ($upload->tipe === 'manhole') {
                    $count += $this->importManholes($upload, $chunk, $needsTransform);
                } else {
                    $count += $this->importPipes($upload, $chunk, $needsTransform);
                }
            }

            $upload->update([
                'total_fitur' => $count,
                'status' => 'completed',
                'metadata' => array_merge($upload->metadata ?? [], [
                    'crs' => $crs,
                    'features_imported' => $count,
                ]),
            ]);

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

        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry'];

            if ($needsTransform) {
                $geometry = $this->coordinateService->transformGeometry($geometry);
            }

            $longitude = $geometry['coordinates'][0];
            $latitude = $geometry['coordinates'][1];

            $kodeManhole = $props['NOMOR_MH'] ?? ('MH-' . uniqid());

            IpalManhole::updateOrCreate(
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
                    'status' => $this->normalizeStatus($props['STATUS'] ?? 'baik'),
                    'wilayah' => $props['KECAMATAN'] ?? null,
                ]
            );

            $count++;
        }

        return $count;
    }

    /**
     * Import pipe features into the database.
     *
     * @param IpalUpload $upload The upload record
     * @param array $features Array of GeoJSON features
     * @param bool $needsTransform Whether coordinates need EPSG transformation
     * @return int Number of pipes imported
     */
    private function importPipes(IpalUpload $upload, array $features, bool $needsTransform): int
    {
        $count = 0;

        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry'];

            if ($needsTransform) {
                $geometry = $this->coordinateService->transformGeometry($geometry);
            }

            $idJalur = $props['ID_JALUR'] ?? null;
            $kodePipa = $idJalur ?? ('PIPE-' . uniqid());

            IpalJaringanPipa::updateOrCreate(
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
                    'status' => $this->normalizeStatus($props['STATUS'] ?? 'baik'),
                    'wilayah' => null,
                ]
            );

            $count++;
        }

        return $count;
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

    private function normalizeStatus(?string $status): string
    {
        $raw = strtolower(trim((string) $status));

        if ($raw === 'aman' || $raw === 'baik') {
            return 'baik';
        }

        if ($raw === 'dalam perbaikan' || $raw === 'perbaikan') {
            return 'perbaikan';
        }

        if ($raw === 'bermasalah' || $raw === 'masalah' || $raw === 'rusak') {
            return 'rusak';
        }

        return 'baik';
    }
}
