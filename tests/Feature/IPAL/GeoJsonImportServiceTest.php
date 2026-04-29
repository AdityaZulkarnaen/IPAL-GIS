<?php

namespace Tests\Feature\IPAL;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalJaringanPipa;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Models\IpalUpload;
use Modules\IPAL\Services\GeoJsonImportService;
use Modules\IPAL\Services\GeoJsonParserService;
use Tests\TestCase;

class GeoJsonImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_manhole_status_is_preserved_on_reupload(): void
    {
        $user = User::factory()->create();
        $importService = app(GeoJsonImportService::class);

        $firstUpload = IpalUpload::create([
            'user_id' => $user->id,
            'tipe' => 'manhole',
            'nama_file_asli' => 'manhole-first.geojson',
            'status' => 'processing',
            'metadata' => [],
        ]);

        $importService->import($firstUpload, $this->manholeGeoJson('MH-001', 'Aman'));

        $manhole = IpalManhole::query()
            ->where('kode_manhole', 'MH-001')
            ->firstOrFail();

        $this->assertSame('baik', $manhole->status);
        $this->assertSame(
            'baik',
            IpalAssetStatus::query()
                ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
                ->where('asset_code', 'MH-001')
                ->value('status')
        );

        IpalAssetStatus::query()
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
            ->where('asset_code', 'MH-001')
            ->update(['status' => 'rusak']);
        $manhole->update(['status' => 'rusak']);

        $secondUpload = IpalUpload::create([
            'user_id' => $user->id,
            'tipe' => 'manhole',
            'nama_file_asli' => 'manhole-second.geojson',
            'status' => 'processing',
            'metadata' => [],
        ]);

        $importService->import($secondUpload, $this->manholeGeoJson('MH-001', 'Aman'));

        $manhole->refresh();

        $this->assertSame($secondUpload->id, $manhole->upload_id);
        $this->assertSame('rusak', $manhole->status);
        $this->assertSame(
            'rusak',
            IpalAssetStatus::query()
                ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
                ->where('asset_code', 'MH-001')
                ->value('status')
        );
    }

    public function test_pipe_without_id_jalur_uses_stable_auto_code_and_preserves_status(): void
    {
        $user = User::factory()->create();
        $importService = app(GeoJsonImportService::class);

        $firstUpload = IpalUpload::create([
            'user_id' => $user->id,
            'tipe' => 'pipe',
            'nama_file_asli' => 'pipe-first.geojson',
            'status' => 'processing',
            'metadata' => [],
        ]);

        $importService->import($firstUpload, $this->pipeGeoJsonWithoutIdJalur('Aman'));

        $pipe = IpalJaringanPipa::query()->where('upload_id', $firstUpload->id)->firstOrFail();
        $this->assertStringStartsWith('PIPE-AUTO-', $pipe->kode_pipa);
        $this->assertNull($pipe->id_jalur);
        $this->assertSame('baik', $pipe->status);

        IpalAssetStatus::query()
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE)
            ->where('asset_code', $pipe->kode_pipa)
            ->update(['status' => 'rusak']);
        $pipe->update(['status' => 'rusak']);

        $secondUpload = IpalUpload::create([
            'user_id' => $user->id,
            'tipe' => 'pipe',
            'nama_file_asli' => 'pipe-second.geojson',
            'status' => 'processing',
            'metadata' => [],
        ]);

        $importService->import($secondUpload, $this->pipeGeoJsonWithoutIdJalur('Aman'));
        $secondUpload->refresh();

        $samePipe = IpalJaringanPipa::query()->where('kode_pipa', $pipe->kode_pipa)->firstOrFail();
        $this->assertSame($secondUpload->id, $samePipe->upload_id);
        $this->assertSame('rusak', $samePipe->status);
        $this->assertSame(
            'rusak',
            IpalAssetStatus::query()
                ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE)
                ->where('asset_code', $pipe->kode_pipa)
                ->value('status')
        );
        $this->assertSame(1, data_get($secondUpload->metadata, 'pipe_identity.without_id_jalur'));
        $this->assertSame(1, data_get($secondUpload->metadata, 'pipe_identity.matched_existing_fingerprint'));
    }

    public function test_parser_counts_pipe_features_without_id_jalur(): void
    {
        $parserService = app(GeoJsonParserService::class);

        $features = [
            [
                'type' => 'Feature',
                'properties' => [
                    'ID_JALUR' => 'J-001',
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [110.0, -7.8],
                        [110.1, -7.9],
                    ],
                ],
            ],
            [
                'type' => 'Feature',
                'properties' => [
                    'PIPE_DIA' => 200,
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [
                        [110.2, -7.81],
                        [110.3, -7.91],
                    ],
                ],
            ],
        ];

        $this->assertSame(1, $parserService->countPipeFeaturesWithoutIdJalur($features));
    }

    private function manholeGeoJson(string $kodeManhole, string $status): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [
                        'NOMOR_MH' => $kodeManhole,
                        'STATUS' => $status,
                        'KECAMATAN' => 'Wirobrajan',
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [110.3533, -7.7991],
                    ],
                ],
            ],
        ];
    }

    private function pipeGeoJsonWithoutIdJalur(string $status): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [
                        'STATUS' => $status,
                        'PIPE_DIA' => 200,
                        'FUNGSI' => 'Utama',
                    ],
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => [
                            [110.3533, -7.7991],
                            [110.3535, -7.7993],
                        ],
                    ],
                ],
            ],
        ];
    }
}
