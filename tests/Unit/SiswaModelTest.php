<?php

namespace Tests\Unit;

use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_fillable_attributes_are_defined_correctly(): void
    {
        $siswa = new Siswa;
        $expectedFillable = [
            'nama_siswa',
            'sekolah',
            'nis',
            'alamat_sekolah',
        ];

        $this->assertEquals($expectedFillable, $siswa->getFillable());
        $this->assertEquals('siswa', $siswa->getTable());
    }

    public function test_siswa_can_be_created_using_factory(): void
    {
        $siswa = Siswa::factory()->create([
            'nama_siswa' => 'Rian Hidayat',
            'sekolah' => 'SMAN 1 Pekanbaru',
            'nis' => '0098765432',
        ]);

        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'nama_siswa' => 'Rian Hidayat',
            'sekolah' => 'SMAN 1 Pekanbaru',
        ]);

        $this->assertEquals('Rian Hidayat', $siswa->nama_siswa);
        $this->assertEquals('0098765432', $siswa->nis);
    }
}
