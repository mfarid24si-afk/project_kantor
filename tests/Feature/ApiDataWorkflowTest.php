<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Komunitas;
use App\Models\Siswa;
use App\Models\Umum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDataWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_guru_counts_returns_accurate_per_kabupaten_aggregation(): void
    {
        Guru::factory()->count(3)->create(['kabupaten' => 'Kampar']);
        Guru::factory()->count(2)->create(['kabupaten' => 'Bengkalis']);
        Guru::factory()->count(1)->create(['kabupaten' => 'Siak']);

        $response = $this->getJson('/api/guru/counts');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals(3, $data['Kampar']);
        $this->assertEquals(2, $data['Bengkalis']);
        $this->assertEquals(1, $data['Siak']);
    }

    public function test_api_siswa_counts_returns_accurate_per_kabupaten_aggregation(): void
    {
        Siswa::factory()->count(4)->create(['kabupaten' => 'Pekanbaru']);
        Siswa::factory()->count(2)->create(['kabupaten' => 'Kampar']);

        $response = $this->getJson('/api/siswa/counts');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(4, $data['Pekanbaru']);
        $this->assertEquals(2, $data['Kampar']);
    }

    public function test_api_komunitas_counts_returns_accurate_per_kabupaten_aggregation(): void
    {
        Komunitas::factory()->count(3)->create(['kabupaten' => 'Bengkalis']);
        Komunitas::factory()->count(1)->create(['kabupaten' => 'Dumai']);

        $response = $this->getJson('/api/komunitas/counts');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(3, $data['Bengkalis']);
        $this->assertEquals(1, $data['Dumai']);
    }

    public function test_api_umum_counts_returns_accurate_per_kabupaten_aggregation(): void
    {
        Umum::factory()->count(5)->create(['kabupaten' => 'Siak']);

        $response = $this->getJson('/api/umum/counts');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(5, $data['Siak']);
    }

    public function test_api_get_table_data_for_all_valid_tables(): void
    {
        Guru::factory()->count(2)->create();
        Siswa::factory()->count(2)->create();
        Komunitas::factory()->count(2)->create();
        Umum::factory()->count(2)->create();

        $tables = ['guru', 'siswa', 'komunitas', 'umum'];

        foreach ($tables as $table) {
            $response = $this->getJson("/api/data/{$table}");
            $response->assertStatus(200);
            $this->assertCount(2, $response->json());
        }
    }

    public function test_api_get_table_data_filtered_by_kabupaten_for_all_entities(): void
    {
        Guru::factory()->count(2)->create(['kabupaten' => 'Pelalawan']);
        Guru::factory()->count(3)->create(['kabupaten' => 'Dumai']);
        Siswa::factory()->count(4)->create(['kabupaten' => 'Pelalawan']);
        Komunitas::factory()->count(1)->create(['kabupaten' => 'Pelalawan']);
        Umum::factory()->count(2)->create(['kabupaten' => 'Pelalawan']);

        $resGuru = $this->getJson('/api/data/guru?kabupaten=Pelalawan');
        $resGuru->assertStatus(200);
        $this->assertCount(2, $resGuru->json());

        $resSiswa = $this->getJson('/api/data/siswa?kabupaten=Pelalawan');
        $resSiswa->assertStatus(200);
        $this->assertCount(4, $resSiswa->json());

        $resKomunitas = $this->getJson('/api/data/komunitas?kabupaten=Pelalawan');
        $resKomunitas->assertStatus(200);
        $this->assertCount(1, $resKomunitas->json());

        $resUmum = $this->getJson('/api/data/umum?kabupaten=Pelalawan');
        $resUmum->assertStatus(200);
        $this->assertCount(2, $resUmum->json());
    }

    public function test_api_get_table_data_returns_404_for_invalid_table(): void
    {
        $response = $this->getJson('/api/data/unknown_table_xyz');

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Tabel tidak ditemukan']);
    }

    public function test_api_insert_single_row_for_each_table(): void
    {
        // 1. Guru
        $resGuru = $this->postJson('/api/data/guru', [
            'nama_guru' => 'Siti Aminah, S.Pd',
            'kabupaten' => 'Kuantansingingi',
            'asal_sekolah' => 'SMAN 1 Teluk Kuantan',
            'nuptk' => '9876543210123456',
        ]);
        $resGuru->assertStatus(201);
        $this->assertDatabaseHas('guru', ['nama_guru' => 'Siti Aminah, S.Pd']);

        // 2. Siswa
        $resSiswa = $this->postJson('/api/data/siswa', [
            'nama_siswa' => 'Bintang Pratama',
            'kabupaten' => 'Siak',
            'sekolah' => 'SMPN 2 Siak',
            'nis' => '12345678',
        ]);
        $resSiswa->assertStatus(201);
        $this->assertDatabaseHas('siswa', ['nama_siswa' => 'Bintang Pratama', 'kabupaten' => 'Siak']);

        // 3. Komunitas
        $resKomunitas = $this->postJson('/api/data/komunitas', [
            'nama_individu' => 'Datuk Maringgai',
            'nama_komunitas' => 'Lembaga Adat Melayu',
            'kabupaten' => 'Pekanbaru',
            'alamat_afiliasi' => 'Pekanbaru',
        ]);
        $resKomunitas->assertStatus(201);
        $this->assertDatabaseHas('komunitas', ['nama_komunitas' => 'Lembaga Adat Melayu', 'kabupaten' => 'Pekanbaru']);

        // 4. Umum
        $resUmum = $this->postJson('/api/data/umum', [
            'nama_umum' => 'Hasan Basri',
            'kabupaten' => 'Dumai',
            'pekerjaan' => 'Wiraswasta',
            'alamat' => 'Dumai Barat',
        ]);
        $resUmum->assertStatus(201);
        $this->assertDatabaseHas('umum', ['nama_umum' => 'Hasan Basri', 'kabupaten' => 'Dumai']);
    }

    public function test_api_insert_single_row_returns_404_for_invalid_table(): void
    {
        $response = $this->postJson('/api/data/invalid_table', [
            'foo' => 'bar',
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Tabel tidak ditemukan']);
    }

    public function test_api_batch_insert_creates_multiple_records(): void
    {
        $batchData = [
            [
                'nama_guru' => 'Guru Batch 1',
                'kabupaten' => 'Rohil',
                'asal_sekolah' => 'SMAN 1 Bagansiapiapi',
                'nuptk' => '1111222233334444',
            ],
            [
                'nama_guru' => 'Guru Batch 2',
                'kabupaten' => 'Rohul',
                'asal_sekolah' => 'SMAN 1 Pasir Pengaraian',
                'nuptk' => '5555666677778888',
            ],
        ];

        $response = $this->postJson('/api/data/guru/batch', $batchData);

        $response->assertStatus(201);
        $response->assertJson(['success' => true, 'inserted' => 2]);

        $this->assertDatabaseHas('guru', ['nama_guru' => 'Guru Batch 1']);
        $this->assertDatabaseHas('guru', ['nama_guru' => 'Guru Batch 2']);
    }

    public function test_api_batch_insert_safely_ignores_extra_columns(): void
    {
        $batchWithExtraCols = [
            [
                'nama_guru' => 'Guru Resilient',
                'kabupaten' => 'Meranti',
                'asal_sekolah' => 'SMAN 1 Selatpanjang',
                'nuptk' => '9999000011112222',
                'nomor_urut_csv' => 1,
                'keterangan_tambahan' => 'Extra data that does not exist in schema',
            ],
        ];

        $response = $this->postJson('/api/data/guru/batch', $batchWithExtraCols);

        $response->assertStatus(201);
        $response->assertJson(['success' => true, 'inserted' => 1]);

        $this->assertDatabaseHas('guru', ['nama_guru' => 'Guru Resilient']);
    }

    public function test_api_batch_insert_returns_400_when_payload_is_not_an_array(): void
    {
        $response = $this->call('POST', '/api/data/guru/batch', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], '"string_payload"');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Payload harus berupa array dan tidak boleh kosong']);
    }

    public function test_api_batch_insert_returns_404_for_invalid_table(): void
    {
        $response = $this->postJson('/api/data/unregistered_table/batch', [
            ['field' => 'value'],
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Tabel tidak ditemukan']);
    }
}
