<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guru>
 */
class GuruFactory extends Factory
{
    protected $model = Guru::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kabupatenList = [
            'Bengkalis', 'Dumai', 'Indragiri Hilir', 'Indragiri Hulu',
            'Kampar', 'Kuantansingingi', 'Meranti', 'Pekanbaru',
            'Pelalawan', 'Rohil', 'Rohul', 'Siak',
        ];

        return [
            'nama_guru' => fake()->name(),
            'kabupaten' => fake()->randomElement($kabupatenList),
            'asal_sekolah' => 'SMAN '.fake()->numberBetween(1, 10).' '.fake()->city(),
            'nuptk' => fake()->numerify('################'),
            'kelurahan' => 'Kelurahan '.fake()->streetName(),
            'provinsi' => 'Riau',
            'nama_guru_utama' => fake()->name(),
        ];
    }
}
