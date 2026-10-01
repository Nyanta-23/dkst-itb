<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'pic_id' => null,
            'code' => strtoupper('PRG-'.fake()->unique()->bothify('####-???')),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(10),
            'year' => now()->year,
            'budget' => fake()->randomFloat(2, 10_000_000, 1_000_000_000),
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
            'status' => 'ongoing',
        ];
    }
}
