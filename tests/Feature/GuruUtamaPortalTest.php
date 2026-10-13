<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Kegiatan;
use App\Models\Maestro;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            'asal_sekolah' => 'SMAN 1 Bengkalis',
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
            'sekolah' => 'SMAN 1 Bengkalis',
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

    public function test_guru_utama_can_download_guru_and_siswa_template(): void
    {
        $responseGuru = $this->actingAs($this->user1)->get('/guru-utama/guru/template');
        $responseGuru->assertStatus(200);
        $responseGuru->assertHeader('Content-Disposition', 'attachment; filename="template_import_guru.csv"');

        $responseSiswa = $this->actingAs($this->user1)->get('/guru-utama/siswa/template');
        $responseSiswa->assertStatus(200);
        $responseSiswa->assertHeader('Content-Disposition', 'attachment; filename="template_import_siswa.csv"');
    }

    public function test_guru_utama_can_import_guru_via_csv(): void
    {
        $csvContent = "nama_guru,asal_sekolah,kabupaten,nuptk,kelurahan\n".
            "Guru Impor Satu,SMPN 1 Bengkalis,Bengkalis,111222,Kota\n".
            "Guru Impor Dua,SMPN 2 Bengkalis,Bengkalis,333444,Desa\n";

        $file = UploadedFile::fake()->createWithContent('import_guru.csv', $csvContent);

        $response = $this->actingAs($this->user1)->post('/guru-utama/guru/import', [
            'file' => $file,
        ]);

        $response->assertRedirect('/guru-utama/guru');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('guru', [
            'nama_guru' => 'Guru Impor Satu',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);
        $this->assertDatabaseHas('guru', [
            'nama_guru' => 'Guru Impor Dua',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);
    }

    public function test_guru_utama_can_import_siswa_via_csv(): void
    {
        $csvContent = "nama_siswa,sekolah,kabupaten,nis,alamat_sekolah\n".
            "Siswa Impor Satu,SMPN 1 Bengkalis,Bengkalis,9991,Jl. Bengkalis\n".
            "Siswa Impor Dua,SMPN 2 Bengkalis,Bengkalis,9992,Jl. Selat\n";

        $file = UploadedFile::fake()->createWithContent('import_siswa.csv', $csvContent);

        $response = $this->actingAs($this->user1)->post('/guru-utama/siswa/import', [
            'file' => $file,
        ]);

        $response->assertRedirect('/guru-utama/siswa');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'nama_siswa' => 'Siswa Impor Satu',
            'sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);
        $this->assertDatabaseHas('siswa', [
            'nama_siswa' => 'Siswa Impor Dua',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);
    }

    public function test_guru_utama_can_edit_and_update_guru(): void
    {
        $guru = Guru::create([
            'nama_guru' => 'Guru Awal',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
            'nama_guru_utama' => $this->guruUtama1->nama,
        ]);

        $resEdit = $this->actingAs($this->user1)->get("/guru-utama/guru/{$guru->id}/edit");
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Guru Awal');

        $resUpdate = $this->actingAs($this->user1)->put("/guru-utama/guru/{$guru->id}", [
            'nama_guru' => 'Guru Terupdate',
            'asal_sekolah' => 'SMAN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nuptk' => '1234567890123456',
        ]);
        $resUpdate->assertRedirect('/guru-utama/guru');

        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'nama_guru' => 'Guru Terupdate',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
        ]);

        // User 2 cannot edit User 1's guru
        $resUnauthorized = $this->actingAs($this->user2)->put("/guru-utama/guru/{$guru->id}", [
            'nama_guru' => 'Hack Guru',
            'asal_sekolah' => 'SMAN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
        ]);
        $resUnauthorized->assertStatus(403);
    }

    public function test_guru_utama_can_edit_and_update_siswa(): void
    {
        $siswa = Siswa::create([
            'nama_siswa' => 'Murid Awal',
            'sekolah' => 'SMPN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
            'nama_guru_utama' => $this->guruUtama1->nama,
        ]);

        $resEdit = $this->actingAs($this->user1)->get("/guru-utama/siswa/{$siswa->id}/edit");
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Murid Awal');

        $resUpdate = $this->actingAs($this->user1)->put("/guru-utama/siswa/{$siswa->id}", [
            'nama_siswa' => 'Murid Terupdate',
            'sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nis' => '8888',
        ]);
        $resUpdate->assertRedirect('/guru-utama/siswa');

        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'nama_siswa' => 'Murid Terupdate',
            'sekolah' => 'SMAN 1 Bengkalis',
        ]);

        // User 2 cannot edit User 1's siswa
        $resUnauthorized = $this->actingAs($this->user2)->put("/guru-utama/siswa/{$siswa->id}", [
            'nama_siswa' => 'Hack Siswa',
            'sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
        ]);
        $resUnauthorized->assertStatus(403);
    }

    public function test_guru_utama_can_export_guru_and_siswa_rekap_csv(): void
    {
        Guru::create([
            'nama_guru' => 'Guru Export Test',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);

        Siswa::create([
            'nama_siswa' => 'Siswa Export Test',
            'sekolah' => 'SMPN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);

        $resGuruExport = $this->actingAs($this->user1)->get('/guru-utama/guru/export');
        $resGuruExport->assertStatus(200);
        $this->assertStringContainsString('Guru Export Test', $resGuruExport->streamedContent());

        $resSiswaExport = $this->actingAs($this->user1)->get('/guru-utama/siswa/export');
        $resSiswaExport->assertStatus(200);
        $this->assertStringContainsString('Siswa Export Test', $resSiswaExport->streamedContent());
    }

    public function test_guru_import_skips_duplicates(): void
    {
        Guru::create([
            'nama_guru' => 'Guru Eksis',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nuptk' => '55555555',
            'guru_utama_id' => $this->guruUtama1->id,
        ]);

        $csvContent = "nama_guru,asal_sekolah,kabupaten,nuptk\n".
            "Guru Eksis,SMAN 1 Bengkalis,Bengkalis,55555555\n".
            "Guru Baru,SMAN 2 Bengkalis,Bengkalis,66666666\n";

        $file = UploadedFile::fake()->createWithContent('import_guru_duplikat.csv', $csvContent);

        $response = $this->actingAs($this->user1)->post('/guru-utama/guru/import', [
            'file' => $file,
        ]);

        $response->assertRedirect('/guru-utama/guru');
        $response->assertSessionHas('success');
        $this->assertStringContainsString('duplikat', session('success'));

        $this->assertCount(2, Guru::where('guru_utama_id', $this->guruUtama1->id)->get());
    }
}
