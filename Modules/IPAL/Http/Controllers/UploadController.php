<?php

namespace Modules\IPAL\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonfigurasiModel;
use Modules\IPAL\Models\IpalUpload;
use Modules\IPAL\Services\GeoJsonParserService;
use Modules\IPAL\Services\GeoJsonImportService;

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

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'tipe' => 'required|in:manhole,pipe',
        ]);

        $file = $request->file('file');
        $tipe = $request->input('tipe');

        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['geojson', 'json'])) {
            return back()->with('error', 'File must be a GeoJSON (.geojson or .json) file.');
        }

        try {
            $geojsonData = $this->parserService->parseFile($file);
            $this->parserService->validate($geojsonData);

            $expectedType = $this->parserService->getExpectedGeometryType($tipe);
            $features = $this->parserService->extractFeatures($geojsonData);
            $this->parserService->validateGeometryType($features, $expectedType);
        } catch (\Exception $e) {
            return back()->with('error', 'GeoJSON validation failed: ' . $e->getMessage());
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
            return back()->with('success', "Successfully imported {$importedCount} features from {$file->getClientOriginalName()}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        $upload = IpalUpload::findOrFail($id);

        if ($upload->tipe === 'manhole') {
            $upload->manholes()->delete();
        } else {
            $upload->pipes()->delete();
        }

        $upload->delete();

        return back()->with('success', 'Upload and associated data deleted.');
    }
}
