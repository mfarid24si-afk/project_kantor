<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Kegiatan;
use App\Models\Maestro;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruUtamaPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;

    protected GuruUtama $guruUtama1;

    protected User $user2;

    protected GuruUtama $guruUtama2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create(['role' => 'guru_utama']);
        $this->guruUtama1 = GuruUtama::create([
            'user_id' => $this->user1->id,
            'nama' => 'Guru Utama Satu',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
        ]);

        $this->user2 = User::factory()->create(['role' => 'guru_utama']);
        $this->guruUtama2 = GuruUtama::create([
            'user_id' => $this->user2->id,
            'nama' => 'Guru Utama Dua',
            'asal_sekolah' => 'SMAN 1 Dumai',
            'kabupaten' => 'Dumai',
        ]);
    }

    public function test_guru_utama_can_view_portal_dashboard(): void
    {
        $maestro = Maestro::create([
            'nama' => 'Maestro Pengiring',
            'bidang_seni' => 'Gendang Melayu',
            'kabupaten' => 'Bengkalis',
        ]);

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Workshop Musik Riau',
            'kabupaten' => 'Bengkalis',
            'status' => 'berlangsung',
        ]);

        $kegiatan->maestros()->attach($maestro->id);
        $kegiatan->guruUtamas()->attach($this->guruUtama1->id);

        $response = $this->actingAs($this->user1)->get('/guru-utama');

        $response->assertStatus(200);
        $response->assertSee('Portal Pengimbasan Guru Utama');
        $response->assertSee('Workshop Musik Riau');
        $response->assertSee('Maestro Pengiring');
    }

    public function test_guru_utama_can_input_guru_taught(): void
    {
        $response = $this->actingAs($this->user1)->post('/guru-utama/guru', [
            'nama_guru' => 'Guru Binaan Baru',
            'asal_sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nuptk' => '9988776655',
            'kelurahan' => 'Bengkalis Kota',
        ]);

        $response->assertRedirect('/guru-utama/guru');
        $this->assertDatabaseHas('guru', [
            'nama_guru' => 'Guru Binaan Baru',
            'asal_sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
            'nama_guru_utama' => $this->guruUtama1->nama,
        ]);
    }

    public function test_guru_utama_can_input_siswa_taught(): void
    {
        $response = $this->actingAs($this->user1)->post('/guru-utama/siswa', [
            'nama_siswa' => 'Siswa Binaan Baru',
            'sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nis' => 'NIS9988',
        ]);

        $response->assertRedirect('/guru-utama/siswa');
        $this->assertDatabaseHas('siswa', [
            'nama_siswa' => 'Siswa Binaan Baru',
            'sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
            'nama_guru_utama' => $this->guruUtama1->nama,
        ]);
    }

    public function test_guru_utama_can_delete_own_recorded_guru(): void
    {
        $guru = Guru::create([
            'nama_guru' => 'Guru Binaan Hapus',
            'asal_sekolah' => 'SMPN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
            'nama_guru_utama' => $this->guruUtama1->nama,
        ]);

        $response = $this->actingAs($this->user1)->delete("/guru-utama/guru/{$guru->id}");

        $response->assertRedirect('/guru-utama/guru');
        $this->assertDatabaseMissing('guru', ['id' => $guru->id]);
    }

    public function test_guru_utama_cannot_delete_guru_recorded_by_another_guru_utama(): void
    {
        $guru = Guru::create([
            'nama_guru' => 'Guru Milik Guru Utama 2',
            'asal_sekolah' => 'SMAN 1 Dumai',
            'kabupaten' => 'Dumai',
            'guru_utama_id' => $this->guruUtama2->id,
            'nama_guru_utama' => $this->guruUtama2->nama,
        ]);

        $response = $this->actingAs($this->user1)->delete("/guru-utama/guru/{$guru->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('guru', ['id' => $guru->id]);
    }

    public function test_guru_utama_cannot_delete_siswa_recorded_by_another_guru_utama(): void
    {
        $siswa = Siswa::create([
            'nama_siswa' => 'Siswa Milik Guru Utama 2',
            'sekolah' => 'SMAN 1 Dumai',
            'kabupaten' => 'Dumai',
            'guru_utama_id' => $this->guruUtama2->id,
            'nama_guru_utama' => $this->guruUtama2->nama,
        ]);

        $response = $this->actingAs($this->user1)->delete("/guru-utama/siswa/{$siswa->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('siswa', ['id' => $siswa->id]);
    }
}
