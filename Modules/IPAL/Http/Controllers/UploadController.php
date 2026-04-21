<?php

namespace Modules\IPAL\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonfigurasiModel;
use Modules\IPAL\Models\IpalUpload;
use Modules\IPAL\Services\GeoJsonParserService;
use Modules\IPAL\Services\GeoJsonImportService;
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

    public function index()
    {
        $data_konfig = KonfigurasiModel::first();
        $service = ['data_konfig' => $data_konfig];

        $toptitle = 'IPAL';
        $title = 'Data Jaringan';
        $subtitle = 'Data Jaringan';

        $uploads = IpalUpload::with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('ipal::upload.index', compact('service', 'toptitle', 'title', 'subtitle', 'uploads'));
    }

    public function history(Request $request)
    {
        $data_konfig = KonfigurasiModel::first();
        $service = ['data_konfig' => $data_konfig];

        $toptitle = 'IPAL';
        $title = 'Data Jaringan';
        $subtitle = 'Riwayat Upload';

        $search = trim((string) $request->query('q', ''));

        $perPage = $request->query('per_page', 10);

        $allowedPerPage = [5, 10, 15, 25];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $uploads = IpalUpload::with('user:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_file_asli', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate($perPage) 
            ->withQueryString(); 

        return view('ipal::upload.history', compact('service', 'toptitle', 'title', 'subtitle', 'uploads', 'search'));
    }

    public function store(Request $request)
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
            return back()->with('error', 'File must be a GeoJSON (.geojson or .json) or Shapefile ZIP (.zip) file.');
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
            return back()->with('error', ($fileType === 'shapefile' ? 'Shapefile' : 'GeoJSON') . ' validation failed: ' . $e->getMessage());
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
            return back()->with('success', "Successfully imported {$importedCount} features from " . ($fileType === 'shapefile' ? 'shapefile' : 'GeoJSON') . " ({$file->getClientOriginalName()}).");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, int $id)
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Upload and associated data deleted.',
            ]);
        }

        return back()->with('success', 'Upload and associated data deleted.');
    }
}
