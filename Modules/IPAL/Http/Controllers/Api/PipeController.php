<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\Aduan;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalJaringanPipa;

class PipeController extends Controller
{
    /**
     * List pipes with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = IpalJaringanPipa::fromActiveUpload()
            ->with('canonicalStatus')
            ->withCount('aduan');

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
            $status = IpalAssetStatus::normalizeStatus((string) $request->status);
            $this->applyCanonicalStatusFilter($query, $status);
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
        $pipes->getCollection()->transform(function (IpalJaringanPipa $pipe) {
            return $this->applyCanonicalStatus($pipe);
        });

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
        $pipe = IpalJaringanPipa::fromActiveUpload()
            ->with('canonicalStatus')
            ->findOrFail($id);
        $this->applyCanonicalStatus($pipe);

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

        $normalizedStatus = IpalAssetStatus::normalizeStatus($validated['status'] ?? null);
        $validated['status'] = $normalizedStatus;

        $pipe = IpalJaringanPipa::create($validated);
        IpalAssetStatus::updateOrCreate(
            [
                'asset_type' => IpalAssetStatus::ASSET_TYPE_PIPE,
                'asset_code' => $pipe->kode_pipa,
            ],
            [
                'asset_id' => $pipe->id,
                'status' => $normalizedStatus,
            ]
        );
        $pipe->load('canonicalStatus');
        $this->applyCanonicalStatus($pipe);

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
        $originalKodePipa = $pipe->kode_pipa;

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

        if (array_key_exists('status', $validated)) {
            $validated['status'] = IpalAssetStatus::normalizeStatus($validated['status']);
        }

        $pipe->update($validated);

        $effectiveStatus = IpalAssetStatus::normalizeStatus($pipe->status);
        if ($pipe->status !== $effectiveStatus) {
            $pipe->update(['status' => $effectiveStatus]);
        }

        if ($originalKodePipa !== $pipe->kode_pipa) {
            IpalAssetStatus::query()
                ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE)
                ->where('asset_code', $originalKodePipa)
                ->delete();
        }

        IpalAssetStatus::updateOrCreate(
            [
                'asset_type' => IpalAssetStatus::ASSET_TYPE_PIPE,
                'asset_code' => $pipe->kode_pipa,
            ],
            [
                'asset_id' => $pipe->id,
                'status' => $effectiveStatus,
            ]
        );

        $pipe->load('canonicalStatus');
        $this->applyCanonicalStatus($pipe);

        return response()->json([
            'success' => true,
            'message' => 'Pipe updated.',
            'data' => $pipe,
        ]);
    }

    /**
     * Delete a pipe.
     */
    public function destroy(int $id): JsonResponse
    {
        $pipe = IpalJaringanPipa::findOrFail($id);

        IpalAssetStatus::query()
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE)
            ->where('asset_code', $pipe->kode_pipa)
            ->delete();

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
        $query = IpalJaringanPipa::fromActiveUpload()
            ->with('canonicalStatus')
            ->addSelect([
                'aduan_count' => Aduan::query()
                    ->join('ipal_jaringan_pipa as pipa_lookup', 'aduan.pipa_id', '=', 'pipa_lookup.id')
                    ->whereColumn('pipa_lookup.kode_pipa', 'ipal_jaringan_pipa.kode_pipa')
                    ->selectRaw('COUNT(*)'),
            ]);

        if ($request->filled('fungsi')) {
            $query->where('fungsi', $request->fungsi);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            $status = IpalAssetStatus::normalizeStatus((string) $request->status);
            $this->applyCanonicalStatusFilter($query, $status);
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
                    'status' => $this->resolvedPipeStatus($pipe),
                    'aduan_count' => $pipe->aduan_count ?? 0,
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
                'fungsi' => IpalJaringanPipa::fromActiveUpload()->distinct()->whereNotNull('fungsi')->pluck('fungsi'),
                'pipe_dia' => IpalJaringanPipa::fromActiveUpload()->distinct()->whereNotNull('pipe_dia')->orderBy('pipe_dia')->pluck('pipe_dia'),
                'tahun' => IpalJaringanPipa::fromActiveUpload()->distinct()->whereNotNull('tahun')->orderBy('tahun')->pluck('tahun'),
                'status' => IpalAssetStatus::query()
                    ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE)
                    ->whereIn('asset_code', IpalJaringanPipa::fromActiveUpload()->select('kode_pipa'))
                    ->distinct()
                    ->whereNotNull('status')
                    ->orderBy('status')
                    ->pluck('status'),
                'material' => IpalJaringanPipa::fromActiveUpload()->distinct()->whereNotNull('material')->pluck('material'),
                'wilayah' => IpalJaringanPipa::fromActiveUpload()->distinct()->whereNotNull('wilayah')->orderBy('wilayah')->pluck('wilayah'),
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

    private function resolvedPipeStatus(IpalJaringanPipa $pipe): string
    {
        $canonicalStatus = $pipe->canonicalStatus?->status;

        if ($canonicalStatus !== null && trim((string) $canonicalStatus) !== '') {
            return IpalAssetStatus::normalizeStatus($canonicalStatus);
        }

        return IpalAssetStatus::normalizeStatus($pipe->status);
    }

    private function applyCanonicalStatus(IpalJaringanPipa $pipe): IpalJaringanPipa
    {
        $pipe->setAttribute('status', $this->resolvedPipeStatus($pipe));
        $pipe->unsetRelation('canonicalStatus');

        return $pipe;
    }
}
