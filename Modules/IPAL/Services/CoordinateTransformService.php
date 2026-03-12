<?php

namespace Modules\IPAL\Services;

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

/**
 * Service for transforming coordinates between EPSG:32749 (UTM Zone 49S) and EPSG:4326 (WGS84).
 */
class CoordinateTransformService
{
    private Proj4php $proj4;
    private Proj $sourceProj;
    private Proj $targetProj;

    public function __construct()
    {
        $this->proj4 = new Proj4php();
        $this->proj4->addDef('EPSG:32749', '+proj=utm +zone=49 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs');
        $this->sourceProj = new Proj('EPSG:32749', $this->proj4);
        $this->targetProj = new Proj('EPSG:4326', $this->proj4);
    }

    /**
     * Transform a single coordinate pair from EPSG:32749 to EPSG:4326.
     *
     * @param float $x Easting (UTM)
     * @param float $y Northing (UTM)
     * @return array [longitude, latitude]
     */
    public function transform(float $x, float $y): array
    {
        $point = new Point($x, $y);
        $result = $this->proj4->transform($this->sourceProj, $this->targetProj, $point);

        return [
            round($result->x, 8),
            round($result->y, 8),
        ];
    }

    /**
     * Transform a GeoJSON Point geometry.
     *
     * @param array $geometry GeoJSON Point geometry with EPSG:32749 coordinates
     * @return array GeoJSON Point geometry with EPSG:4326 coordinates
     */
    public function transformPoint(array $geometry): array
    {
        $coords = $geometry['coordinates'];
        $transformed = $this->transform($coords[0], $coords[1]);

        return [
            'type' => 'Point',
            'coordinates' => $transformed,
        ];
    }

    /**
     * Transform a GeoJSON MultiLineString geometry.
     *
     * @param array $geometry GeoJSON MultiLineString geometry with EPSG:32749 coordinates
     * @return array GeoJSON MultiLineString geometry with EPSG:4326 coordinates
     */
    public function transformMultiLineString(array $geometry): array
    {
        $transformedLines = [];

        foreach ($geometry['coordinates'] as $line) {
            $transformedLine = [];
            foreach ($line as $point) {
                $transformedLine[] = $this->transform($point[0], $point[1]);
            }
            $transformedLines[] = $transformedLine;
        }

        return [
            'type' => 'MultiLineString',
            'coordinates' => $transformedLines,
        ];
    }

    /**
     * Transform any supported GeoJSON geometry.
     *
     * @param array $geometry GeoJSON geometry object
     * @return array Transformed GeoJSON geometry in EPSG:4326
     * @throws \InvalidArgumentException If geometry type is not supported
     */
    public function transformGeometry(array $geometry): array
    {
        $type = $geometry['type'] ?? null;

        switch ($type) {
            case 'Point':
                return $this->transformPoint($geometry);
            case 'MultiLineString':
                return $this->transformMultiLineString($geometry);
            case 'LineString':
                return $this->transformLineString($geometry);
            default:
                throw new \InvalidArgumentException("Unsupported geometry type: {$type}");
        }
    }

    /**
     * Transform a GeoJSON LineString geometry.
     *
     * @param array $geometry GeoJSON LineString geometry with EPSG:32749 coordinates
     * @return array GeoJSON LineString geometry with EPSG:4326 coordinates
     */
    public function transformLineString(array $geometry): array
    {
        $transformedCoords = [];

        foreach ($geometry['coordinates'] as $point) {
            $transformedCoords[] = $this->transform($point[0], $point[1]);
        }

        return [
            'type' => 'LineString',
            'coordinates' => $transformedCoords,
        ];
    }
}
