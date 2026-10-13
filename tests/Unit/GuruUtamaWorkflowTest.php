<?php

namespace Tests\Unit;

use App\Models\Guru;
use App\Models\GuruUtama;
use App\Models\Kegiatan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GuruUtamaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji bahwa akun Guru Utama yang dibuat dapat melakukan autentikasi login.
     */
    public function test_guru_utama_can_authenticate_with_created_credentials(): void
    {
        $user = User::create([
            'name' => 'Guru Utama Test',
            'email' => 'test.guru@beringin.id',
            'role' => 'guru_utama',
            'password' => Hash::make('password123'),
        ]);

        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Utama Test',
            'asal_sekolah' => 'SMAN 1 Siak',
            'kabupaten' => 'Siak',
        ]);

        $this->assertTrue($user->isGuruUtama());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertEquals($guruUtama->id, $user->guruUtama->id);
    }

    /**
     * Uji bahwa Guru Utama dapat mengaitkan dan menyimpan data guru yang diajarkannya.
     */
    public function test_guru_utama_can_record_and_own_taught_guru(): void
    {
        $user = User::factory()->create(['role' => 'guru_utama']);
        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Dra. Fatimah',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
        ]);

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Workshop Tari Zapin 2026',
            'kabupaten' => 'Bengkalis',
            'tahun' => 2026,
            'status' => 'berlangsung',
        ]);

        $guru = Guru::create([
            'nama_guru' => 'Siti Nurhaliza, S.Pd.',
            'asal_sekolah' => 'SMPN 2 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nuptk' => '1234567890123456',
            'guru_utama_id' => $guruUtama->id,
            'nama_guru_utama' => $guruUtama->nama,
            'kegiatan_id' => $kegiatan->id,
        ]);

        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'nama_guru' => 'Siti Nurhaliza, S.Pd.',
            'guru_utama_id' => $guruUtama->id,
        ]);

        // Cek relasi Eloquent
        $this->assertCount(1, $guruUtama->gurus);
        $this->assertEquals('Siti Nurhaliza, S.Pd.', $guruUtama->gurus->first()->nama_guru);
    }

    /**
     * Uji bahwa Guru Utama dapat mengaitkan dan menyimpan data siswa yang diajarkannya.
     */
    public function test_guru_utama_can_record_and_own_taught_siswa(): void
    {
        $user = User::factory()->create(['role' => 'guru_utama']);
        $guruUtama = GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Dra. Fatimah',
            'asal_sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
        ]);

        $siswa = Siswa::create([
            'nama_siswa' => 'Rizky Ramadhan',
            'sekolah' => 'SMAN 1 Bengkalis',
            'kabupaten' => 'Bengkalis',
            'nis' => '2026001',
            'guru_utama_id' => $guruUtama->id,
            'nama_guru_utama' => $guruUtama->nama,
        ]);

        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'nama_siswa' => 'Rizky Ramadhan',
            'guru_utama_id' => $guruUtama->id,
        ]);

        $this->assertCount(1, $guruUtama->siswas);
        $this->assertEquals('Rizky Ramadhan', $guruUtama->siswas->first()->nama_siswa);
    }
}
