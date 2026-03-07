<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\IpalUpload;
use Modules\IPAL\Services\GeoJsonImportService;
use Modules\IPAL\Services\GeoJsonParserService;

class UploadController extends Controller
{
    private GeoJsonParserService $parserService;
    private GeoJsonImportService $importService;

    public function __construct(
        GeoJsonParserService $parserService,
        GeoJsonImportService $importService
    ) {
        $this->parserService = $parserService;
        $this->importService = $importService;
    }

    /**
     * List all uploads with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = IpalUpload::with('user:id,name')
            ->orderBy('created_at', 'desc');

        if ($request->has('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $uploads = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $uploads,
        ]);
    }

    /**
     * Show a single upload detail.
     */
    public function show(int $id): JsonResponse
    {
        $upload = IpalUpload::with('user:id,name')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $upload,
        ]);
    }

    /**
     * Upload and process a GeoJSON file.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'tipe' => 'required|in:manhole,pipe',
        ]);

        $file = $request->file('file');
        $tipe = $request->input('tipe');

        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['geojson', 'json'])) {
            return response()->json([
                'success' => false,
                'message' => 'File must be a GeoJSON (.geojson or .json) file.',
            ], 422);
        }

        try {
            $geojsonData = $this->parserService->parseFile($file);
            $this->parserService->validate($geojsonData);

            $expectedType = $this->parserService->getExpectedGeometryType($tipe);
            $features = $this->parserService->extractFeatures($geojsonData);
            $this->parserService->validateGeometryType($features, $expectedType);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'GeoJSON validation failed: ' . $e->getMessage(),
            ], 422);
        }

        $upload = IpalUpload::create([
            'user_id' => $request->user()->id,
            'tipe' => $tipe,
            'nama_file_asli' => $file->getClientOriginalName(),
            'status' => 'processing',
            'metadata' => [
                'crs' => $this->parserService->extractCrs($geojsonData),
                'total_features_in_file' => count($features),
            ],
        ]);

        try {
            $importedCount = $this->importService->import($upload, $geojsonData);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} features.",
                'data' => $upload->fresh(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'data' => $upload->fresh(),
            ], 500);
        }
    }

    /**
     * Delete an upload and its associated features.
     */
    public function destroy(int $id): JsonResponse
    {
        $upload = IpalUpload::findOrFail($id);

        if ($upload->tipe === 'manhole') {
            $upload->manholes()->delete();
        } else {
            $upload->pipes()->delete();
        }

        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'Upload and associated data deleted.',
        ]);
    }
}
