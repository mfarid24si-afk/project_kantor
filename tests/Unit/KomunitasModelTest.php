<?php

namespace Tests\Unit;

use App\Models\Komunitas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KomunitasModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_komunitas_fillable_attributes_are_defined_correctly(): void
    {
        $komunitas = new Komunitas;
        $expectedFillable = [
            'nama_individu',
            'nama_komunitas',
            'alamat_afiliasi',
        ];

        $this->assertEquals($expectedFillable, $komunitas->getFillable());
        $this->assertEquals('komunitas', $komunitas->getTable());
    }

    public function test_komunitas_can_be_created_using_factory(): void
    {
        $komunitas = Komunitas::factory()->create([
            'nama_individu' => 'Tengku Muhammad',
            'nama_komunitas' => 'Komunitas Sastra Melayu',
            'alamat_afiliasi' => 'Jl. Sudirman No. 12 Pekanbaru',
        ]);

        $this->assertDatabaseHas('komunitas', [
            'id' => $komunitas->id,
            'nama_komunitas' => 'Komunitas Sastra Melayu',
            'nama_individu' => 'Tengku Muhammad',
        ]);

        $this->assertEquals('Komunitas Sastra Melayu', $komunitas->nama_komunitas);
    }
}
