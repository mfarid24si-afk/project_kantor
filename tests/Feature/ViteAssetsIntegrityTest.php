<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViteAssetsIntegrityTest extends TestCase
{
    public function test_vite_manifest_exists_and_contains_entrypoints(): void
    {
        $manifestPath = public_path('build/manifest.json');

        $this->assertFileExists($manifestPath);
        $this->assertFileIsReadable($manifestPath);

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $this->assertIsArray($manifest);
        $this->assertArrayHasKey('resources/js/app.js', $manifest);
        $this->assertArrayHasKey('resources/css/style.css', $manifest);
    }

    public function test_district_logo_assets_exist_for_all_twelve_kabupaten(): void
    {
        $logos = [
            'logo-bengkalis.png',
            'logo-dumai.png',
            'logo-indragiri-hilir.png',
            'logo-indragiri-hulu.png',
            'logo-kampar.png',
            'logo-kuansing.PNG',
            'logo-meranti.png',
            'logo-pekanbaru.png',
            'logo-pelalawan.png',
            'logo-rohil.png',
            'logo-rohul.png',
            'logo-siak.png',
        ];

        foreach ($logos as $logo) {
            $path = public_path("assets/images/{$logo}");
            $this->assertFileExists($path, "Logo asset missing: {$logo}");
            $this->assertFileIsReadable($path);
        }
    }

    public function test_institution_logos_exist(): void
    {
        $this->assertFileExists(public_path('assets/images/logo-bbpr.png'));
        $this->assertFileExists(public_path('assets/images/bbpr.jpeg'));
        $this->assertFileExists(public_path('assets/images/logo-pcr.webp'));
        $this->assertFileExists(public_path('assets/images/pcr.jpg'));
    }

    public function test_favicon_exists_and_is_valid(): void
    {
        $faviconPath = public_path('favicon.svg');

        $this->assertFileExists($faviconPath);
        $this->assertFileIsReadable($faviconPath);
        $this->assertStringContainsString('<svg', file_get_contents($faviconPath));
    }
}
