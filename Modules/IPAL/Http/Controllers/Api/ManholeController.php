<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalManhole;

class ManholeController extends Controller
{
    /**
     * List manholes with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = IpalManhole::fromActiveUpload()->with('canonicalStatus');

        if ($request->filled('desa')) {
            $query->where('desa', 'like', '%' . $request->desa . '%');
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', 'like', '%' . $request->kecamatan . '%');
        }

        if ($request->filled('kondisi_mh')) {
            $query->where('kondisi_mh', $request->kondisi_mh);
        }

        if ($request->filled('risiko')) {
            $query->where('risiko', $request->risiko);
        }

        if ($request->filled('klasifikasi')) {
            $query->where('klasifikasi', $request->klasifikasi);
        }

        if ($request->filled('status')) {
            $status = IpalAssetStatus::normalizeStatus((string) $request->status);
            $this->applyCanonicalStatusFilter($query, $status);
        }

        if ($request->filled('bentuk')) {
            $query->where('bentuk', $request->bentuk);
        }

        if ($request->filled('material_mh')) {
            $query->where('material_mh', $request->material_mh);
        }

        if ($request->filled('sektor')) {
            $query->where('sektor', $request->sektor);
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah', 'like', '%' . $request->wilayah . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_manhole', 'like', '%' . $search . '%')
                  ->orWhere('desa', 'like', '%' . $search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $search . '%')
                  ->orWhere('surveyor', 'like', '%' . $search . '%')
                  ->orWhere('wilayah', 'like', '%' . $search . '%');
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'kode_manhole', 'desa', 'kecamatan', 'kondisi_mh', 'risiko', 'status'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $manholes = $query->paginate($request->get('per_page', 15));
        $manholes->getCollection()->transform(function (IpalManhole $manhole) {
            return $this->applyCanonicalStatus($manhole);
        });

        return response()->json([
            'success' => true,
            'data' => $manholes,
        ]);
    }

    /**
     * Show a single manhole.
     */
    public function show(int $id): JsonResponse
    {
        $manhole = IpalManhole::fromActiveUpload()
            ->with(['logs', 'canonicalStatus'])
            ->findOrFail($id);
        $this->applyCanonicalStatus($manhole);

        return response()->json([
            'success' => true,
            'data' => $manhole,
        ]);
    }

    /**
     * Create a new manhole.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_manhole' => 'required|string|unique:ipal_manholes,kode_manhole',
            'bentuk' => 'nullable|string',
            'dim_mh' => 'nullable|numeric',
            'panjang' => 'nullable|numeric',
            'lebar' => 'nullable|numeric',
            'kedalaman' => 'nullable|numeric',
            'material_mh' => 'nullable|string',
            'struktur_mh' => 'nullable|string',
            'kondisi_mh' => 'nullable|string',
            'sedimen' => 'nullable|numeric',
            'jarak_pipa' => 'nullable|numeric',
            'ukuran_pipa' => 'nullable|numeric',
            'material_pipa' => 'nullable|string',
            'sekitar' => 'nullable|string',
            'surveyor' => 'nullable|string',
            'desa' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'ketinggian' => 'nullable|numeric',
            'topografi' => 'nullable|string',
            'jenis_tanah' => 'nullable|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'foto_1' => 'nullable|string',
            'foto_2' => 'nullable|string',
            'foto_3' => 'nullable|string',
            'foto_4' => 'nullable|string',
            'probabilitas' => 'nullable|numeric',
            'dampak' => 'nullable|numeric',
            'tingkat_risiko' => 'nullable|numeric',
            'risiko' => 'nullable|string',
            'klasifikasi' => 'nullable|string',
            'pengendali' => 'nullable|string',
            'sektor' => 'nullable|integer',
            'status' => 'nullable|string',
            'wilayah' => 'nullable|string',
        ]);

        $validated['geometry'] = [
            'type' => 'Point',
            'coordinates' => [(float) $validated['longitude'], (float) $validated['latitude']],
        ];

        $normalizedStatus = IpalAssetStatus::normalizeStatus($validated['status'] ?? null);
        $validated['status'] = $normalizedStatus;

        $manhole = IpalManhole::create($validated);
        IpalAssetStatus::updateOrCreate(
            [
                'asset_type' => IpalAssetStatus::ASSET_TYPE_MANHOLE,
                'asset_code' => $manhole->kode_manhole,
            ],
            [
                'asset_id' => $manhole->id,
                'status' => $normalizedStatus,
            ]
        );
        $manhole->load('canonicalStatus');
        $this->applyCanonicalStatus($manhole);

        return response()->json([
            'success' => true,
            'message' => 'Manhole created.',
            'data' => $manhole,
        ], 201);
    }

    /**
     * Update an existing manhole.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $manhole = IpalManhole::findOrFail($id);
        $originalKodeManhole = $manhole->kode_manhole;

        $validated = $request->validate([
            'kode_manhole' => 'sometimes|string|unique:ipal_manholes,kode_manhole,' . $id,
            'bentuk' => 'nullable|string',
            'dim_mh' => 'nullable|numeric',
            'panjang' => 'nullable|numeric',
            'lebar' => 'nullable|numeric',
            'kedalaman' => 'nullable|numeric',
            'material_mh' => 'nullable|string',
            'struktur_mh' => 'nullable|string',
            'kondisi_mh' => 'nullable|string',
            'sedimen' => 'nullable|numeric',
            'jarak_pipa' => 'nullable|numeric',
            'ukuran_pipa' => 'nullable|numeric',
            'material_pipa' => 'nullable|string',
            'sekitar' => 'nullable|string',
            'surveyor' => 'nullable|string',
            'desa' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'ketinggian' => 'nullable|numeric',
            'topografi' => 'nullable|string',
            'jenis_tanah' => 'nullable|string',
            'longitude' => 'sometimes|numeric',
            'latitude' => 'sometimes|numeric',
            'foto_1' => 'nullable|string',
            'foto_2' => 'nullable|string',
            'foto_3' => 'nullable|string',
            'foto_4' => 'nullable|string',
            'probabilitas' => 'nullable|numeric',
            'dampak' => 'nullable|numeric',
            'tingkat_risiko' => 'nullable|numeric',
            'risiko' => 'nullable|string',
            'klasifikasi' => 'nullable|string',
            'pengendali' => 'nullable|string',
            'sektor' => 'nullable|integer',
            'status' => 'nullable|string',
            'wilayah' => 'nullable|string',
        ]);

        if (isset($validated['longitude']) || isset($validated['latitude'])) {
            $lng = $validated['longitude'] ?? $manhole->longitude;
            $lat = $validated['latitude'] ?? $manhole->latitude;
            $validated['geometry'] = [
                'type' => 'Point',
                'coordinates' => [(float) $lng, (float) $lat],
            ];
        }

        if (array_key_exists('status', $validated)) {
            $validated['status'] = IpalAssetStatus::normalizeStatus($validated['status']);
        }

        $manhole->update($validated);

        $effectiveStatus = IpalAssetStatus::normalizeStatus($manhole->status);
        if ($manhole->status !== $effectiveStatus) {
            $manhole->update(['status' => $effectiveStatus]);
        }

        if ($originalKodeManhole !== $manhole->kode_manhole) {
            IpalAssetStatus::query()
                ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
                ->where('asset_code', $originalKodeManhole)
                ->delete();
        }

        IpalAssetStatus::updateOrCreate(
            [
                'asset_type' => IpalAssetStatus::ASSET_TYPE_MANHOLE,
                'asset_code' => $manhole->kode_manhole,
            ],
            [
                'asset_id' => $manhole->id,
                'status' => $effectiveStatus,
            ]
        );

        $manhole->load('canonicalStatus');
        $this->applyCanonicalStatus($manhole);

        return response()->json([
            'success' => true,
            'message' => 'Manhole updated.',
            'data' => $manhole,
        ]);
    }

    /**
     * Delete a manhole.
     */
    public function destroy(int $id): JsonResponse
    {
        $manhole = IpalManhole::findOrFail($id);

        IpalAssetStatus::query()
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
            ->where('asset_code', $manhole->kode_manhole)
            ->delete();

        $manhole->delete();

        return response()->json([
            'success' => true,
            'message' => 'Manhole deleted.',
        ]);
    }

    /**
     * Export manholes as GeoJSON FeatureCollection.
     */
    public function geojson(Request $request): JsonResponse
    {
        $query = IpalManhole::fromActiveUpload()->with('canonicalStatus');

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        if ($request->filled('kondisi_mh')) {
            $query->where('kondisi_mh', $request->kondisi_mh);
        }

        if ($request->filled('risiko')) {
            $query->where('risiko', $request->risiko);
        }

        if ($request->filled('status')) {
            $status = IpalAssetStatus::normalizeStatus((string) $request->status);
            $this->applyCanonicalStatusFilter($query, $status);
        }

        if ($request->filled('sektor')) {
            $query->where('sektor', $request->sektor);
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah', 'like', '%' . $request->wilayah . '%');
        }

        $manholes = $query->get();

        $features = $manholes->map(function ($manhole) {
            return [
                'type' => 'Feature',
                'geometry' => $manhole->geometry,
                'properties' => [
                    'id' => $manhole->id,
                    'kode_manhole' => $manhole->kode_manhole,
                    'bentuk' => $manhole->bentuk,
                    'material_mh' => $manhole->material_mh,
                    'kondisi_mh' => $manhole->kondisi_mh,
                    'risiko' => $manhole->risiko,
                    'klasifikasi' => $manhole->klasifikasi,
                    'status' => $this->resolvedManholeStatus($manhole),
                    'desa' => $manhole->desa,
                    'kecamatan' => $manhole->kecamatan,
                    'sektor' => $manhole->sektor,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features->values(),
        ]);
    }

    /**
     * Get available filter options for manholes.
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'kondisi_mh' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('kondisi_mh')->pluck('kondisi_mh'),
                'risiko' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('risiko')->pluck('risiko'),
                'klasifikasi' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('klasifikasi')->pluck('klasifikasi'),
                'kecamatan' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('kecamatan')->pluck('kecamatan'),
                'bentuk' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('bentuk')->pluck('bentuk'),
                'material_mh' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('material_mh')->pluck('material_mh'),
                'status' => IpalAssetStatus::query()
                    ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE)
                    ->whereIn('asset_code', IpalManhole::fromActiveUpload()->select('kode_manhole'))
                    ->distinct()
                    ->whereNotNull('status')
                    ->orderBy('status')
                    ->pluck('status'),
                'sektor' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('sektor')->pluck('sektor'),
                'wilayah' => IpalManhole::fromActiveUpload()->distinct()->whereNotNull('wilayah')->orderBy('wilayah')->pluck('wilayah'),
            ],
        ]);
    }

    private function applyCanonicalStatusFilter(Builder $query, string $status): void
    {
        $query->where(function (Builder $statusQuery) use ($status) {
            $statusQuery
                ->whereHas('canonicalStatus', function (Builder $canonicalQuery) use ($status) {
                    $canonicalQuery->where('status', $status);
                })
                ->orWhere(function (Builder $fallbackQuery) use ($status) {
                    $fallbackQuery
                        ->whereDoesntHave('canonicalStatus')
                        ->where('status', $status);
                });
        });
    }

    private function resolvedManholeStatus(IpalManhole $manhole): string
    {
        $canonicalStatus = $manhole->canonicalStatus?->status;

        if ($canonicalStatus !== null && trim((string) $canonicalStatus) !== '') {
            return IpalAssetStatus::normalizeStatus($canonicalStatus);
        }

        return IpalAssetStatus::normalizeStatus($manhole->status);
    }

    private function applyCanonicalStatus(IpalManhole $manhole): IpalManhole
    {
        $manhole->setAttribute('status', $this->resolvedManholeStatus($manhole));
        $manhole->unsetRelation('canonicalStatus');

        return $manhole;
    }
}
