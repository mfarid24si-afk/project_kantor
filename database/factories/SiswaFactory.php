<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_siswa' => fake()->name(),
            'sekolah' => 'SMAN '.fake()->numberBetween(1, 10).' '.fake()->city(),
            'nis' => fake()->numerify('##########'),
            'alamat_sekolah' => fake()->address(),
        ];
    }
}
