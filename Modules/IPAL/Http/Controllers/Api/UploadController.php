<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\IpalUpload;
use Modules\IPAL\Services\GeoJsonImportService;
use Modules\IPAL\Services\GeoJsonParserService;
use Modules\IPAL\Services\ShapefileParserService;

class UploadController extends Controller
{
    private GeoJsonParserService $parserService;
    private ShapefileParserService $shapefileService;
    private GeoJsonImportService $importService;

    public function __construct(
        GeoJsonParserService $parserService,
        ShapefileParserService $shapefileService,
        GeoJsonImportService $importService
    ) {
        $this->parserService = $parserService;
        $this->shapefileService = $shapefileService;
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

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'tipe' => 'required|in:manhole,pipe',
        ]);

        $file = $request->file('file');
        $tipe = $request->input('tipe');

        $extension = strtolower($file->getClientOriginalExtension());
        
        $isGeoJson = in_array($extension, ['geojson', 'json']);
        $isShapefile = $extension === 'zip';
        
        if (!$isGeoJson && !$isShapefile) {
            return response()->json([
                'success' => false,
                'message' => 'File must be a GeoJSON (.geojson or .json) or Shapefile ZIP (.zip) file.',
            ], 422);
        }

        try {
            if ($isGeoJson) {
                $geojsonData = $this->parserService->parseFile($file);
                $fileType = 'geojson';
                $metadata = [
                    'original_format' => 'geojson',
                ];
            } else {
                $geojsonData = $this->shapefileService->parseZipFile($file);
                $fileType = 'shapefile';
                $shapefileName = $this->shapefileService->extractShapefileNameFromZip($file);
                $zipContents = $this->shapefileService->getZipFileList($file);
                $metadata = [
                    'original_format' => 'shapefile',
                    'shapefile_name' => $shapefileName,
                    'zip_contents' => $zipContents,
                ];
            }
            
            $this->parserService->validate($geojsonData);

            $expectedType = $this->parserService->getExpectedGeometryType($tipe);
            $features = $this->parserService->extractFeatures($geojsonData);
            $this->parserService->validateGeometryType($features, $expectedType);
            $pipeWithoutIdJalurCount = $tipe === 'pipe'
                ? $this->parserService->countPipeFeaturesWithoutIdJalur($features)
                : 0;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ($fileType === 'shapefile' ? 'Shapefile' : 'GeoJSON') . ' validation failed: ' . $e->getMessage(),
            ], 422);
        }

        $upload = IpalUpload::create([
            'user_id' => $request->user()->id,
            'tipe' => $tipe,
            'nama_file_asli' => $file->getClientOriginalName(),
            'status' => 'processing',
            'metadata' => array_merge($metadata, [
                'crs' => $this->parserService->extractCrs($geojsonData),
                'total_features_in_file' => count($features),
                'pipe_features_without_id_jalur' => $pipeWithoutIdJalurCount ?? 0,
            ]),
        ]);

        try {
            $importedCount = $this->importService->import($upload, $geojsonData);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} features from " . ($fileType === 'shapefile' ? 'shapefile' : 'GeoJSON') . ".",
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
        $tipe = $upload->tipe;

        if ($upload->tipe === 'manhole') {
            $upload->manholes()->delete();
        } else {
            $upload->pipes()->delete();
        }

        $upload->delete();

        IpalUpload::setLatestAsActive($tipe);

        return response()->json([
            'success' => true,
            'message' => 'Upload and associated data deleted.',
        ]);
    }
}
