<?php

namespace Modules\IPAL\Services;

use Illuminate\Http\UploadedFile;

/**
 * Service for parsing and validating GeoJSON files.
 */
class GeoJsonParserService
{
    /**
     * Parse a GeoJSON file and return the decoded data.
     *
     * @param UploadedFile $file Uploaded GeoJSON file
     * @return array Decoded GeoJSON data
     * @throws \RuntimeException If file cannot be read or parsed
     */
    public function parseFile(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());

        if ($content === false) {
            throw new \RuntimeException('Failed to read GeoJSON file.');
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Parse a GeoJSON string and return the decoded data.
     *
     * @param string $content GeoJSON string content
     * @return array Decoded GeoJSON data
     * @throws \RuntimeException If content cannot be parsed
     */
    public function parseString(string $content): array
    {
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Validate that the parsed data is a valid GeoJSON FeatureCollection.
     *
     * @param array $data Parsed GeoJSON data
     * @throws \RuntimeException If validation fails
     */
    public function validate(array $data): void
    {
        if (($data['type'] ?? null) !== 'FeatureCollection') {
            throw new \RuntimeException('GeoJSON must be a FeatureCollection.');
        }

        if (!isset($data['features']) || !is_array($data['features'])) {
            throw new \RuntimeException('GeoJSON FeatureCollection must contain a features array.');
        }

        if (empty($data['features'])) {
            throw new \RuntimeException('GeoJSON FeatureCollection must contain at least one feature.');
        }
    }

    /**
     * Validate that all features match the expected geometry type.
     *
     * @param array $features Array of GeoJSON features
     * @param string $expectedType Expected geometry type (Point, MultiLineString, etc.)
     * @throws \RuntimeException If any feature has an unexpected geometry type
     */
    public function validateGeometryType(array $features, string $expectedType): void
    {
        $allowedTypes = $this->getAllowedGeometryTypes($expectedType);
        
        foreach ($features as $feature) {
            $type = $feature['geometry']['type'] ?? null;

            if (!in_array($type, $allowedTypes)) {
                $allowedTypesStr = implode(' or ', $allowedTypes);
                
                $suggestion = $this->getTypeSuggestion($type);
                
                throw new \RuntimeException(
                    "Geometry type mismatch: File contains '{$type}' geometry, but expected {$allowedTypesStr}. {$suggestion}"
                );
            }
        }
    }

    public function countPipeFeaturesWithoutIdJalur(array $features): int
    {
        $missingCount = 0;

        foreach ($features as $index => $feature) {
            $props = $feature['properties'] ?? [];
            $idJalur = trim((string) ($props['ID_JALUR'] ?? ''));

            if ($idJalur === '') {
                $missingCount++;
            }
        }

        return $missingCount;
    }

    private function getAllowedGeometryTypes(string $primaryType): array
    {
        if ($primaryType === 'MultiLineString') {
            return ['LineString', 'MultiLineString'];
        }
        
        return [$primaryType];
    }

    private function getTypeSuggestion(string $actualType): string
    {
        if ($actualType === 'Point') {
            return "This appears to be manhole data. Please select 'manhole' as the upload type.";
        }
        
        if (in_array($actualType, ['LineString', 'MultiLineString'])) {
            return "This appears to be pipe/network data. Please select 'pipe' as the upload type.";
        }
        
        return "Please verify your file and selected upload type.";
    }

    /**
     * Extract CRS information from GeoJSON data.
     *
     * @param array $data Parsed GeoJSON data
     * @return string|null CRS name or null if not specified
     */
    public function extractCrs(array $data): ?string
    {
        return $data['crs']['properties']['name'] ?? null;
    }

    /**
     * Extract features from the parsed GeoJSON data.
     *
     * @param array $data Parsed GeoJSON data
     * @return array Array of GeoJSON features
     */
    public function extractFeatures(array $data): array
    {
        return $data['features'] ?? [];
    }

    /**
     * Determine the expected geometry type based on upload type.
     *
     * @param string $tipe Upload type (manhole or pipe)
     * @return string Expected GeoJSON geometry type
     */
    public function getExpectedGeometryType(string $tipe): string
    {
        return match ($tipe) {
            'manhole' => 'Point',
            'pipe' => 'MultiLineString',
            default => throw new \InvalidArgumentException("Unknown upload type: {$tipe}"),
        };
    }
}
