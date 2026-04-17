<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Models\IpalJaringanPipa;

class StatisticsController extends Controller
{
    /**
     * Return aggregated statistics for IPAL network data.
     *
     * Optional query params:
     *   wilayah    — filter both manholes and pipes by wilayah column
     *   kecamatan  — filter manholes by kecamatan column
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'wilayah'   => ['nullable', 'string', 'max:255'],
                'kecamatan' => ['nullable', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filter parameters.',
                'errors'  => $e->errors(),
            ], 422);
        }

        $wilayah   = $validated['wilayah']   ?? null;
        $kecamatan = $validated['kecamatan'] ?? null;

        try {
            $manholeStats = $this->manholeStatistics($wilayah, $kecamatan);
            $pipaStats    = $this->pipaStatistics($wilayah);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'manhole' => $manholeStats,
                'pipa'    => $pipaStats,
            ],
            'filters_applied' => [
                'wilayah'   => $wilayah,
                'kecamatan' => $kecamatan,
            ],
        ]);
    }

    private function manholeStatistics(?string $wilayah, ?string $kecamatan): array
    {
        $query = IpalManhole::fromActiveUpload();

        if ($wilayah !== null) {
            $query->where('wilayah', 'like', '%' . $wilayah . '%');
        }

        if ($kecamatan !== null) {
            $query->where('kecamatan', 'like', '%' . $kecamatan . '%');
        }

        $total = (clone $query)->count();

        $rawByStatus = (clone $query)
            ->leftJoin('ipal_asset_statuses as asset_status', function ($join) {
                $join->on('asset_status.asset_code', '=', 'ipal_manholes.kode_manhole')
                    ->where('asset_status.asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE);
            })
            ->selectRaw('LOWER(COALESCE(asset_status.status, ipal_manholes.status)) as status, COUNT(*) as total')
            ->whereRaw('COALESCE(asset_status.status, ipal_manholes.status) IS NOT NULL')
            ->groupBy(DB::raw('LOWER(COALESCE(asset_status.status, ipal_manholes.status))'))
            ->pluck('total', 'status')
            ->toArray();

        $byStatus = $this->normalizeStatusTotals($rawByStatus);

        $byKondisi = (clone $query)
            ->selectRaw('kondisi_mh, COUNT(*) as total')
            ->whereNotNull('kondisi_mh')
            ->groupBy('kondisi_mh')
            ->pluck('total', 'kondisi_mh')
            ->toArray();

        return [
            'total'     => $total,
            'by_status' => $byStatus,
            'by_kondisi' => $byKondisi,
        ];
    }

    private function pipaStatistics(?string $wilayah): array
    {
        $query = IpalJaringanPipa::fromActiveUpload();

        if ($wilayah !== null) {
            $query->where('wilayah', 'like', '%' . $wilayah . '%');
        }

        $total = (clone $query)->count();

        $totalPanjangKm = (float) round(
            (clone $query)->sum('length_km'),
            2
        );

        $rawByStatus = (clone $query)
            ->leftJoin('ipal_asset_statuses as asset_status', function ($join) {
                $join->on('asset_status.asset_code', '=', 'ipal_jaringan_pipa.kode_pipa')
                    ->where('asset_status.asset_type', IpalAssetStatus::ASSET_TYPE_PIPE);
            })
            ->selectRaw('LOWER(COALESCE(asset_status.status, ipal_jaringan_pipa.status)) as status, COUNT(*) as total')
            ->whereRaw('COALESCE(asset_status.status, ipal_jaringan_pipa.status) IS NOT NULL')
            ->groupBy(DB::raw('LOWER(COALESCE(asset_status.status, ipal_jaringan_pipa.status))'))
            ->pluck('total', 'status')
            ->toArray();

        $byStatus = $this->normalizeStatusTotals($rawByStatus);

        $byFungsi = collect((clone $query)
            ->selectRaw('fungsi, COUNT(*) as total')
            ->whereNotNull('fungsi')
            ->groupBy('fungsi')
            ->pluck('total', 'fungsi')
            ->toArray())
            ->mapWithKeys(fn ($v, $k) => [ucfirst(strtolower($k)) => $v])
            ->toArray();

        return [
            'total'            => $total,
            'total_panjang_km' => $totalPanjangKm,
            'by_status'        => $byStatus,
            'by_fungsi'        => $byFungsi,
        ];
    }

    private function normalizeStatusTotals(array $rawTotals): array
    {
        $normalized = [
            'baik' => 0,
            'perbaikan' => 0,
            'rusak' => 0,
        ];

        foreach ($rawTotals as $status => $total) {
            $key = IpalAssetStatus::normalizeStatus($status);
            if (!array_key_exists($key, $normalized)) {
                continue;
            }
            $normalized[$key] += (int) $total;
        }

        return $normalized;
    }
}
