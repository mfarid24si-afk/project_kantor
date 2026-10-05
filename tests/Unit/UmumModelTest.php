<?php

namespace Tests\Unit;

use App\Models\Umum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmumModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_umum_fillable_attributes_are_defined_correctly(): void
    {
        $umum = new Umum;
        $expectedFillable = [
            'nama_umum',
            'pekerjaan',
            'alamat',
        ];

        $this->assertEquals($expectedFillable, $umum->getFillable());
        $this->assertEquals('umum', $umum->getTable());
    }

    public function test_umum_can_be_created_using_factory(): void
    {
        $umum = Umum::factory()->create([
            'nama_umum' => 'Dra. Salmah',
            'pekerjaan' => 'Pemerhati Budaya',
            'alamat' => 'Siak Sri Indrapura',
        ]);

        $this->assertDatabaseHas('umum', [
            'id' => $umum->id,
            'nama_umum' => 'Dra. Salmah',
            'pekerjaan' => 'Pemerhati Budaya',
        ]);

        $this->assertEquals('Dra. Salmah', $umum->nama_umum);
    }
}
