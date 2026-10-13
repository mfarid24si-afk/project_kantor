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

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
        ]);
    }

    public function test_super_admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Panel Kendali Pengimbasan');
        $response->assertSee('Total Kegiatan');
        $response->assertSee('Maestro Terdaftar');
        $response->assertSee('Guru Utama');
    }

    public function test_super_admin_sidebar_displays_menus_in_correct_order(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Dashboard Admin',
            'Akun Guru Utama',
            'Data Maestro',
            'Kelola Kegiatan',
        ]);

        $responseGuruUtama = $this->actingAs($this->admin)->get('/admin/guru-utama');
        $responseGuruUtama->assertStatus(200);
        $responseGuruUtama->assertSee('Akun Guru Utama');

        $responseMaestro = $this->actingAs($this->admin)->get('/admin/maestro');
        $responseMaestro->assertStatus(200);
        $responseMaestro->assertSee('Data Maestro');

        $responseKegiatan = $this->actingAs($this->admin)->get('/admin/kegiatan');
        $responseKegiatan->assertStatus(200);
        $responseKegiatan->assertSee('Kelola Kegiatan');
    }

    public function test_super_admin_can_create_maestro(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/maestro', [
            'nama' => 'Maestro Tradisi',
            'bidang_seni' => 'Seni Tari Zapin',
            'kabupaten' => 'Siak',
            'kontak' => '08123456789',
            'deskripsi' => 'Pengembang tari zapin istana Siak.',
        ]);

        $response->assertRedirect('/admin/maestro');
        $this->assertDatabaseHas('maestros', [
            'nama' => 'Maestro Tradisi',
            'bidang_seni' => 'Seni Tari Zapin',
            'kabupaten' => 'Siak',
        ]);
    }

    public function test_super_admin_can_update_maestro(): void
    {
        $maestro = Maestro::create([
            'nama' => 'Nama Lama',
            'bidang_seni' => 'Seni Musik',
            'kabupaten' => 'Pekanbaru',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/maestro/{$maestro->id}", [
            'nama' => 'Nama Baru',
            'bidang_seni' => 'Seni Musik Tradisi Melayu',
            'kabupaten' => 'Kampar',
            'kontak' => '0811223344',
            'deskripsi' => 'Deskripsi baru.',
        ]);

        $response->assertRedirect('/admin/maestro');
        $this->assertDatabaseHas('maestros', [
            'id' => $maestro->id,
            'nama' => 'Nama Baru',
            'kabupaten' => 'Kampar',
        ]);
    }

    public function test_super_admin_can_delete_maestro(): void
    {
        $maestro = Maestro::create([
            'nama' => 'Maestro Hapus',
            'bidang_seni' => 'Teater',
            'kabupaten' => 'Bengkalis',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/maestro/{$maestro->id}");

        $response->assertRedirect('/admin/maestro');
        $this->assertDatabaseMissing('maestros', ['id' => $maestro->id]);
    }

    public function test_super_admin_can_create_guru_utama_with_login_account(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/guru-utama', [
            'nama' => 'Guru Utama Hebat',
            'asal_sekolah' => 'SMAN 1 Pekanbaru',
            'kabupaten' => 'Pekanbaru',
            'nuptk' => '1234567890123456',
            'nip' => '198001012005011001',
            'no_hp' => '08123456789',
            'email' => 'guru.baru@beringin.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/guru-utama');
        $this->assertDatabaseHas('users', [
            'email' => 'guru.baru@beringin.id',
            'role' => 'guru_utama',
        ]);
        $this->assertDatabaseHas('guru_utamas', [
            'nama' => 'Guru Utama Hebat',
            'asal_sekolah' => 'SMAN 1 Pekanbaru',
            'kabupaten' => 'Pekanbaru',
        ]);
    }

    public function test_super_admin_can_update_guru_utama(): void
    {
        $user = User::factory()->create([
            'email' => 'lama@beringin.id',
            'role' => 'guru_utama',
        ]);

        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Utama Lama',
            'asal_sekolah' => 'SMPN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/guru-utama/{$guruUtama->id}", [
            'nama' => 'Guru Utama Diperbarui',
            'asal_sekolah' => 'SMAN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'email' => 'baru@beringin.id',
        ]);

        $response->assertRedirect('/admin/guru-utama');
        $this->assertDatabaseHas('guru_utamas', [
            'id' => $guruUtama->id,
            'nama' => 'Guru Utama Diperbarui',
            'asal_sekolah' => 'SMAN 2 Bengkalis',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'baru@beringin.id',
        ]);
    }

    public function test_super_admin_can_delete_guru_utama_and_its_user(): void
    {
        $user = User::factory()->create(['role' => 'guru_utama']);
        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Dihapus',
            'asal_sekolah' => 'SMAN 1 Dumai',
            'kabupaten' => 'Dumai',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/guru-utama/{$guruUtama->id}");

        $response->assertRedirect('/admin/guru-utama');
        $this->assertDatabaseMissing('guru_utamas', ['id' => $guruUtama->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_super_admin_can_create_kegiatan_and_assign_maestro_and_guru_utama(): void
    {
        $maestro = Maestro::create([
            'nama' => 'Maestro Uji',
            'bidang_seni' => 'Seni Ukir',
            'kabupaten' => 'Kampar',
        ]);

        $user = User::factory()->create(['role' => 'guru_utama']);
        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Utama Uji',
            'asal_sekolah' => 'SMAN 1 Kampar',
            'kabupaten' => 'Kampar',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/kegiatan', [
            'nama_kegiatan' => 'Festival Budaya Kampar 2026',
            'kabupaten' => 'Kampar',
            'tahun' => 2026,
            'tempat' => 'Bangkinang Kota',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-03',
            'status' => 'direncanakan',
            'maestro_ids' => [$maestro->id],
            'guru_utama_ids' => [$guruUtama->id],
        ]);

        $response->assertRedirect('/admin/kegiatan');
        $this->assertDatabaseHas('kegiatans', [
            'nama_kegiatan' => 'Festival Budaya Kampar 2026',
            'kabupaten' => 'Kampar',
            'tahun' => 2026,
        ]);

        $kegiatan = Kegiatan::where('nama_kegiatan', 'Festival Budaya Kampar 2026')->first();
        $this->assertTrue($kegiatan->maestros->contains($maestro));
        $this->assertTrue($kegiatan->guruUtamas->contains($guruUtama));
    }

    public function test_super_admin_can_view_kegiatan_show_page_with_details(): void
    {
        $maestro = Maestro::create([
            'nama' => 'Maestro Detail',
            'bidang_seni' => 'Pantun',
            'kabupaten' => 'Pelalawan',
        ]);

        $user = User::factory()->create(['role' => 'guru_utama']);
        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Utama Detail',
            'asal_sekolah' => 'SMAN 1 Pangkalan Kerinci',
            'kabupaten' => 'Pelalawan',
        ]);

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Latihan Pantun Riau',
            'kabupaten' => 'Pelalawan',
            'status' => 'berlangsung',
        ]);

        $kegiatan->maestros()->attach($maestro->id);
        $kegiatan->guruUtamas()->attach($guruUtama->id);

        Guru::create([
            'nama_guru' => 'Guru Peserta',
            'asal_sekolah' => 'SMPN 1 Pelalawan',
            'kabupaten' => 'Pelalawan',
            'guru_utama_id' => $guruUtama->id,
            'kegiatan_id' => $kegiatan->id,
        ]);

        Siswa::create([
            'nama_siswa' => 'Siswa Peserta',
            'sekolah' => 'SMPN 1 Pelalawan',
            'kabupaten' => 'Pelalawan',
            'guru_utama_id' => $guruUtama->id,
            'kegiatan_id' => $kegiatan->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/kegiatan/{$kegiatan->id}");

        $response->assertStatus(200);
        $response->assertSee('Latihan Pantun Riau');
        $response->assertSee('Maestro Detail');
        $response->assertSee('Guru Utama Detail');
        $response->assertSee('Guru Peserta');
        $response->assertSee('Siswa Peserta');
    }
}
