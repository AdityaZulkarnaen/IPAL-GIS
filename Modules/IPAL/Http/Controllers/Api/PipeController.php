<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\IpalJaringanPipa;

class PipeController extends Controller
{
    /**
     * List pipes with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = IpalJaringanPipa::query();

        if ($request->filled('fungsi')) {
            $query->where('fungsi', $request->fungsi);
        }

        if ($request->filled('pipe_dia')) {
            $query->where('pipe_dia', $request->pipe_dia);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('material')) {
            $query->where('material', $request->material);
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah', 'like', '%' . $request->wilayah . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pipa', 'like', '%' . $search . '%')
                  ->orWhere('id_jalur', 'like', '%' . $search . '%')
                  ->orWhere('fungsi', 'like', '%' . $search . '%')
                  ->orWhere('wilayah', 'like', '%' . $search . '%');
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'kode_pipa', 'pipe_dia', 'fungsi', 'length_km', 'tahun', 'status'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $pipes = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $pipes,
        ]);
    }

    /**
     * Show a single pipe.
     */
    public function show(int $id): JsonResponse
    {
        $pipe = IpalJaringanPipa::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pipe,
        ]);
    }

    /**
     * Create a new pipe.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_pipa' => 'required|string|unique:ipal_jaringan_pipa,kode_pipa',
            'id_jalur' => 'nullable|string',
            'pipe_dia' => 'nullable|numeric',
            'fungsi' => 'nullable|string',
            'length_km' => 'nullable|numeric',
            'tahun' => 'nullable|integer',
            'source' => 'nullable|string',
            'material' => 'nullable|string',
            'geometry' => 'required|array',
            'geometry.type' => 'required|string|in:MultiLineString,LineString',
            'geometry.coordinates' => 'required|array',
            'status' => 'nullable|string',
            'wilayah' => 'nullable|string',
        ]);

        $pipe = IpalJaringanPipa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pipe created.',
            'data' => $pipe,
        ], 201);
    }

    /**
     * Update an existing pipe.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $pipe = IpalJaringanPipa::findOrFail($id);

        $validated = $request->validate([
            'kode_pipa' => 'sometimes|string|unique:ipal_jaringan_pipa,kode_pipa,' . $id,
            'id_jalur' => 'nullable|string',
            'pipe_dia' => 'nullable|numeric',
            'fungsi' => 'nullable|string',
            'length_km' => 'nullable|numeric',
            'tahun' => 'nullable|integer',
            'source' => 'nullable|string',
            'material' => 'nullable|string',
            'geometry' => 'sometimes|array',
            'geometry.type' => 'required_with:geometry|string|in:MultiLineString,LineString',
            'geometry.coordinates' => 'required_with:geometry|array',
            'status' => 'nullable|string',
            'wilayah' => 'nullable|string',
        ]);

        $pipe->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pipe updated.',
            'data' => $pipe->fresh(),
        ]);
    }

    /**
     * Delete a pipe.
     */
    public function destroy(int $id): JsonResponse
    {
        $pipe = IpalJaringanPipa::findOrFail($id);
        $pipe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pipe deleted.',
        ]);
    }

    /**
     * Export pipes as GeoJSON FeatureCollection.
     */
    public function geojson(Request $request): JsonResponse
    {
        $query = IpalJaringanPipa::query();

        if ($request->filled('fungsi')) {
            $query->where('fungsi', $request->fungsi);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah', 'like', '%' . $request->wilayah . '%');
        }

        $pipes = $query->get();

        $features = $pipes->map(function ($pipe) {
            return [
                'type' => 'Feature',
                'geometry' => $pipe->geometry,
                'properties' => [
                    'id' => $pipe->id,
                    'kode_pipa' => $pipe->kode_pipa,
                    'id_jalur' => $pipe->id_jalur,
                    'pipe_dia' => $pipe->pipe_dia,
                    'fungsi' => $pipe->fungsi,
                    'length_km' => $pipe->length_km,
                    'tahun' => $pipe->tahun,
                    'status' => $pipe->status,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features->values(),
        ]);
    }

    /**
     * Get available filter options for pipes.
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'fungsi' => IpalJaringanPipa::distinct()->whereNotNull('fungsi')->pluck('fungsi'),
                'pipe_dia' => IpalJaringanPipa::distinct()->whereNotNull('pipe_dia')->orderBy('pipe_dia')->pluck('pipe_dia'),
                'tahun' => IpalJaringanPipa::distinct()->whereNotNull('tahun')->orderBy('tahun')->pluck('tahun'),
                'status' => IpalJaringanPipa::distinct()->whereNotNull('status')->pluck('status'),
                'material' => IpalJaringanPipa::distinct()->whereNotNull('material')->pluck('material'),
                'wilayah' => IpalJaringanPipa::distinct()->whereNotNull('wilayah')->orderBy('wilayah')->pluck('wilayah'),
            ],
        ]);
    }
}
