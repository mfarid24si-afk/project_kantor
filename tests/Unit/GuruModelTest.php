<?php

namespace Tests\Unit;

use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_fillable_attributes_are_defined_correctly(): void
    {
        $guru = new Guru;
        $expectedFillable = [
            'nama_guru',
            'kabupaten',
            'asal_sekolah',
            'nuptk',
            'kelurahan',
            'provinsi',
            'nama_guru_utama',
        ];

        $this->assertEquals($expectedFillable, $guru->getFillable());
        $this->assertEquals('guru', $guru->getTable());
    }

    public function test_guru_can_be_created_using_factory(): void
    {
        $guru = Guru::factory()->create([
            'nama_guru' => 'Ahmad Dahlan',
            'kabupaten' => 'Kampar',
            'asal_sekolah' => 'SMAN 1 Bangkinang',
            'nuptk' => '1234567890123456',
        ]);

        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'nama_guru' => 'Ahmad Dahlan',
            'kabupaten' => 'Kampar',
        ]);

        $this->assertEquals('Ahmad Dahlan', $guru->nama_guru);
        $this->assertEquals('Kampar', $guru->kabupaten);
        $this->assertEquals('Riau', $guru->provinsi);
    }

    public function test_guru_can_be_filtered_by_kabupaten(): void
    {
        Guru::factory()->create(['kabupaten' => 'Bengkalis']);
        Guru::factory()->create(['kabupaten' => 'Bengkalis']);
        Guru::factory()->create(['kabupaten' => 'Siak']);

        $bengkalisGurus = Guru::where('kabupaten', 'Bengkalis')->get();
        $siakGurus = Guru::where('kabupaten', 'Siak')->get();

        $this->assertCount(2, $bengkalisGurus);
        $this->assertCount(1, $siakGurus);
    }
}
