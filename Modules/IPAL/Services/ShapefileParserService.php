<?php

namespace Modules\IPAL\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class ShapefileParserService
{
    private GeoJsonParserService $geoJsonParser;

    public function __construct(GeoJsonParserService $geoJsonParser)
    {
        $this->geoJsonParser = $geoJsonParser;
    }

    public function parseZipFile(UploadedFile $file): array
    {
        $tempDir = $this->createTempDirectory();
        
        try {
            $this->extractZipFile($file, $tempDir);
            
            $shpFile = $this->findShapefileInDirectory($tempDir);
            
            $this->validateShapefileComponents($shpFile);
            
            $geojsonData = $this->convertShapefileToGeoJson($shpFile);
            
            $this->geoJsonParser->validate($geojsonData);
            
            return $geojsonData;
            
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    private function createTempDirectory(): string
    {
        $tempDir = storage_path('app/temp/shapefile_' . Str::uuid());
        
        if (!File::makeDirectory($tempDir, 0755, true)) {
            throw new \RuntimeException('Failed to create temporary directory.');
        }
        
        return $tempDir;
    }

    private function extractZipFile(UploadedFile $file, string $targetDir): void
    {
        $zip = new ZipArchive();
        $opened = $zip->open($file->getRealPath());
        
        if ($opened !== true) {
            throw new \RuntimeException('Failed to open ZIP file: ' . $this->getZipErrorMessage($opened));
        }
        
        if ($zip->numFiles === 0) {
            $zip->close();
            throw new \RuntimeException('ZIP file is empty.');
        }
        
        $extracted = $zip->extractTo($targetDir);
        $zip->close();
        
        if (!$extracted) {
            throw new \RuntimeException('Failed to extract ZIP file contents.');
        }
    }

    private function findShapefileInDirectory(string $directory): string
    {
        $files = File::allFiles($directory);
        $shpFiles = [];
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'shp') {
                $shpFiles[] = $file->getRealPath();
            }
        }
        
        if (empty($shpFiles)) {
            throw new \RuntimeException('No .shp file found in ZIP archive.');
        }
        
        if (count($shpFiles) > 1) {
            throw new \RuntimeException('Multiple .shp files found in ZIP. Please include only one shapefile per upload.');
        }
        
        return $shpFiles[0];
    }

    private function validateShapefileComponents(string $shpFilePath): void
    {
        $basePath = substr($shpFilePath, 0, -4);
        
        $requiredExtensions = ['shp', 'dbf', 'shx'];
        $missingFiles = [];
        
        foreach ($requiredExtensions as $ext) {
            $filePath = $basePath . '.' . $ext;
            if (!file_exists($filePath)) {
                $missingFiles[] = $ext;
            }
        }
        
        if (!empty($missingFiles)) {
            throw new \RuntimeException(
                'Missing required shapefile components: ' . implode(', ', $missingFiles) . 
                '. A shapefile requires .shp, .dbf, and .shx files.'
            );
        }
    }

    private function convertShapefileToGeoJson(string $shpFilePath): array
    {
        $geojsonPath = sys_get_temp_dir() . '/converted_' . Str::uuid() . '.geojson';
        
        $command = sprintf(
            'ogr2ogr -f GeoJSON %s %s -t_srs EPSG:4326 2>&1',
            escapeshellarg($geojsonPath),
            escapeshellarg($shpFilePath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            @unlink($geojsonPath);
            throw new \RuntimeException(
                'Failed to convert shapefile to GeoJSON: ' . implode("\n", $output)
            );
        }
        
        if (!file_exists($geojsonPath)) {
            throw new \RuntimeException('Conversion succeeded but GeoJSON file was not created.');
        }
        
        $geojsonContent = file_get_contents($geojsonPath);
        @unlink($geojsonPath);
        
        if ($geojsonContent === false) {
            throw new \RuntimeException('Failed to read converted GeoJSON file.');
        }
        
        $geojsonData = json_decode($geojsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse converted GeoJSON: ' . json_last_error_msg());
        }
        
        return $geojsonData;
    }

    private function cleanupDirectory(string $directory): void
    {
        if (file_exists($directory)) {
            File::deleteDirectory($directory);
        }
    }

    private function getZipErrorMessage(int $code): string
    {
        $errors = [
            ZipArchive::ER_EXISTS => 'File already exists',
            ZipArchive::ER_INCONS => 'Zip archive inconsistent',
            ZipArchive::ER_INVAL => 'Invalid argument',
            ZipArchive::ER_MEMORY => 'Memory allocation failure',
            ZipArchive::ER_NOENT => 'No such file',
            ZipArchive::ER_NOZIP => 'Not a zip archive',
            ZipArchive::ER_OPEN => 'Cannot open file',
            ZipArchive::ER_READ => 'Read error',
            ZipArchive::ER_SEEK => 'Seek error',
        ];
        
        return $errors[$code] ?? 'Unknown error (code: ' . $code . ')';
    }

    public function extractShapefileNameFromZip(UploadedFile $file): ?string
    {
        $zip = new ZipArchive();
        $opened = $zip->open($file->getRealPath());
        
        if ($opened !== true) {
            return null;
        }
        
        $shpFileName = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (substr($filename, -4) === '.shp') {
                $shpFileName = basename($filename);
                break;
            }
        }
        
        $zip->close();
        
        return $shpFileName;
    }

    public function getZipFileList(UploadedFile $file): array
    {
        $zip = new ZipArchive();
        $opened = $zip->open($file->getRealPath());
        
        if ($opened !== true) {
            return [];
        }
        
        $files = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $files[] = $zip->getNameIndex($i);
        }
        
        $zip->close();
        
        return $files;
    }
}
