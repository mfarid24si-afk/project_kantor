<?php

namespace Tests\Feature;

use Tests\TestCase;

class GeoJsonIntegrityTest extends TestCase
{
    public function test_geojson_file_exists_on_disk(): void
    {
        $filePath = public_path('geojson/Area_Kab_Riau.geojson');

        $this->assertFileExists($filePath);
        $this->assertFileIsReadable($filePath);
    }

    public function test_geojson_is_accessible_via_http(): void
    {
        $response = $this->get('/geojson/Area_Kab_Riau.geojson');

        $response->assertStatus(200);
    }

    public function test_geojson_structure_contains_valid_feature_collection(): void
    {
        $filePath = public_path('geojson/Area_Kab_Riau.geojson');
        $raw = file_get_contents($filePath);
        $data = json_decode($raw, true);

        $this->assertIsArray($data);
        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertArrayHasKey('features', $data);
        $this->assertCount(12, $data['features']);

        $expectedDistricts = [
            'Rohil', 'Meranti', 'Bengkalis', 'Dumai', 'Siak',
            'Kampar', 'Pelalawan', 'Kuantansingingi', 'Indragiri Hulu',
            'Indragiri Hilir', 'Pekanbaru', 'Rohul',
        ];

        $foundDistricts = [];

        foreach ($data['features'] as $feature) {
            $this->assertEquals('Feature', $feature['type']);
            $this->assertArrayHasKey('properties', $feature);
            $this->assertArrayHasKey('Keterangan', $feature['properties']);
            $this->assertArrayHasKey('geometry', $feature);
            $this->assertContains($feature['geometry']['type'], ['Polygon', 'MultiPolygon']);
            $this->assertNotEmpty($feature['geometry']['coordinates']);

            $foundDistricts[] = $feature['properties']['Keterangan'];
        }

        foreach ($expectedDistricts as $district) {
            $this->assertContains($district, $foundDistricts);
        }
    }
}
