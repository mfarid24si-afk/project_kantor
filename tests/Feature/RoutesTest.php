<?php

namespace Tests\Feature;

use App\Models\GuruUtama;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Peta Pengimbasan');
        $response->assertSee('Bahasa Melayu Riau');
        $response->assertSee('id="stat-kab"', false);
    }

    public function test_peta_page_is_accessible(): void
    {
        $response = $this->get('/peta');
        $response->assertStatus(200);
        $response->assertSee('Sebaran Data Riau');
        $response->assertSee('id="map"', false);
        $response->assertSee('Bengkalis');
    }

    public function test_form_route_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/form');
        $response->assertRedirect('/login');
    }

    public function test_form_route_redirects_authenticated_guru_utama_to_portal(): void
    {
        $user = User::factory()->create(['role' => 'guru_utama']);
        GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Pengajar',
            'kabupaten' => 'Siak',
        ]);

        $response = $this->actingAs($user)->get('/form');
        $response->assertRedirect('/guru-utama/guru');
    }

    public function test_kabupaten_index_redirects_to_peta(): void
    {
        $response = $this->get('/kabupaten');
        $response->assertRedirect('/peta');
    }

    public function test_kabupaten_detail_is_accessible(): void
    {
        $response = $this->get('/kabupaten/Bengkalis');
        $response->assertStatus(200);
        $response->assertSee('Detail Bengkalis');
        $response->assertSee('data-kabupaten="Bengkalis"', false);
    }

    public function test_tentang_page_is_accessible(): void
    {
        $response = $this->get('/tentang');
        $response->assertStatus(200);
        $response->assertSee('Tentang Kami');
        $response->assertSee('Balai Bahasa Provinsi Riau');
        $response->assertSee('Politeknik Caltex Riau');
    }

    public function test_geojson_file_is_accessible(): void
    {
        $filePath = public_path('geojson/Area_Kab_Riau.geojson');
        $this->assertFileExists($filePath);

        $json = json_decode(file_get_contents($filePath), true);
        $this->assertIsArray($json);
        $this->assertEquals('FeatureCollection', $json['type']);
        $this->assertGreaterThan(0, count($json['features']));
    }

    public function test_local_api_endpoints(): void
    {
        // 1. Counts endpoints
        $resGuruCounts = $this->get('/api/guru/counts');
        $resGuruCounts->assertStatus(200);
        $this->assertIsArray($resGuruCounts->json());

        $resSiswaCounts = $this->get('/api/siswa/counts');
        $resSiswaCounts->assertStatus(200);
        $this->assertIsArray($resSiswaCounts->json());

        $resKomunitasCounts = $this->get('/api/komunitas/counts');
        $resKomunitasCounts->assertStatus(200);
        $this->assertIsArray($resKomunitasCounts->json());

        $resUmumCounts = $this->get('/api/umum/counts');
        $resUmumCounts->assertStatus(200);
        $this->assertIsArray($resUmumCounts->json());

        // 2. Table data
        $resGuru = $this->get('/api/data/guru');
        $resGuru->assertStatus(200);
        $this->assertIsArray($resGuru->json());

        // 3. Filtered by kabupaten
        $resFiltered = $this->get('/api/data/guru?kabupaten=Bengkalis');
        $resFiltered->assertStatus(200);
        $this->assertIsArray($resFiltered->json());

        // 4. Insert row
        $resInsert = $this->postJson('/api/data/guru', [
            'nama_guru' => 'Budi Santoso',
            'kabupaten' => 'Bengkalis',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'nuptk' => '12345678',
        ]);
        $resInsert->assertStatus(201);
        $this->assertDatabaseHas('guru', ['nama_guru' => 'Budi Santoso']);
    }
}
