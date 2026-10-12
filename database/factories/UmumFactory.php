<?php

namespace Database\Factories;

use App\Models\Umum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Umum>
 */
class UmumFactory extends Factory
{
    protected $model = Umum::class;

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
            'nama_umum' => fake()->name(),
            'kabupaten' => fake()->randomElement($kabupatenList),
            'pekerjaan' => fake()->jobTitle(),
            'alamat' => fake()->address(),
        ];
    }
}
