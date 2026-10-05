<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_hero_features_and_links(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Peta Pengimbasan');
        $response->assertSee('Bahasa Melayu Riau');
        $response->assertSee('id="stat-kab"', false);
        $response->assertSee('id="stat-guru"', false);
        $response->assertSee('data-route="/peta"', false);
        $response->assertSee('data-route="/form"', false);
        $response->assertSee('data-route="/tentang"', false);
    }

    public function test_peta_page_displays_map_container_and_districts(): void
    {
        $response = $this->get('/peta');

        $response->assertStatus(200);
        $response->assertSee('Sebaran Data Riau');
        $response->assertSee('id="map"', false);
        $response->assertSee('Bengkalis');
        $response->assertSee('Kampar');
        $response->assertSee('Pekanbaru');
        $response->assertSee('Siak');
        $response->assertSee('Dumai');
    }

    public function test_form_page_displays_single_input_and_csv_upload_forms(): void
    {
        $response = $this->get('/form');

        $response->assertStatus(200);
        $response->assertSee('Input Data');
        $response->assertSee('Upload Data Massal (CSV)');
        $response->assertSee('id="data-form"', false);
        $response->assertSee('id="upload-input"', false);
        $response->assertSee('id="btn-upload"', false);
    }

    public function test_kabupaten_index_redirects_to_peta(): void
    {
        $response = $this->get('/kabupaten');

        $response->assertRedirect('/peta');
    }

    public function test_kabupaten_detail_page_loads_for_multiple_districts(): void
    {
        $districts = ['Bengkalis', 'Kampar', 'Pekanbaru', 'Siak'];

        foreach ($districts as $district) {
            $response = $this->get("/kabupaten/{$district}");
            $response->assertStatus(200);
            $response->assertSee("Detail {$district}");
            $response->assertSee("data-kabupaten=\"{$district}\"", false);
        }
    }

    public function test_tentang_page_displays_institutional_information(): void
    {
        $response = $this->get('/tentang');

        $response->assertStatus(200);
        $response->assertSee('Tentang Kami');
        $response->assertSee('Balai Bahasa Provinsi Riau');
        $response->assertSee('Politeknik Caltex Riau');
    }

    public function test_non_existent_page_returns_404(): void
    {
        $response = $this->get('/halaman-yang-pasti-tidak-ada-12345');

        $response->assertStatus(404);
    }
}
