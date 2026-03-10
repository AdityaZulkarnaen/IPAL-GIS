<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\IpalManhole;

class ManholeController extends Controller
{
    /**
     * List manholes with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = IpalManhole::query();

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
            $query->where('status', $request->status);
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
        $manhole = IpalManhole::with('logs')->findOrFail($id);

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

        $manhole = IpalManhole::create($validated);

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

        $manhole->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Manhole updated.',
            'data' => $manhole->fresh(),
        ]);
    }

    /**
     * Delete a manhole.
     */
    public function destroy(int $id): JsonResponse
    {
        $manhole = IpalManhole::findOrFail($id);
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
        $query = IpalManhole::query();

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
            $query->where('status', $request->status);
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
                    'status' => $manhole->status,
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
                'kondisi_mh' => IpalManhole::distinct()->whereNotNull('kondisi_mh')->pluck('kondisi_mh'),
                'risiko' => IpalManhole::distinct()->whereNotNull('risiko')->pluck('risiko'),
                'klasifikasi' => IpalManhole::distinct()->whereNotNull('klasifikasi')->pluck('klasifikasi'),
                'kecamatan' => IpalManhole::distinct()->whereNotNull('kecamatan')->pluck('kecamatan'),
                'bentuk' => IpalManhole::distinct()->whereNotNull('bentuk')->pluck('bentuk'),
                'material_mh' => IpalManhole::distinct()->whereNotNull('material_mh')->pluck('material_mh'),
                'status' => IpalManhole::distinct()->whereNotNull('status')->pluck('status'),
                'sektor' => IpalManhole::distinct()->whereNotNull('sektor')->pluck('sektor'),
                'wilayah' => IpalManhole::distinct()->whereNotNull('wilayah')->orderBy('wilayah')->pluck('wilayah'),
            ],
        ]);
    }
}
