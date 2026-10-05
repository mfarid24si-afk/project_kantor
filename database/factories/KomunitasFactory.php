<?php

namespace Database\Factories;

use App\Models\Komunitas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Komunitas>
 */
class KomunitasFactory extends Factory
{
    protected $model = Komunitas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_individu' => fake()->name(),
            'nama_komunitas' => 'Komunitas Seni & Budaya '.fake()->word(),
            'alamat_afiliasi' => fake()->address(),
        ];
    }
}
