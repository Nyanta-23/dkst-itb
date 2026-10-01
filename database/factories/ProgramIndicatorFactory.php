<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\ProgramIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramIndicator>
 */
class ProgramIndicatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => fake()->sentence(3),
            'is_iku' => false,
            'iku_code' => null,
            'target' => fake()->randomFloat(2, 1, 100),
            'measurement_unit' => 'unit',
        ];
    }
}
