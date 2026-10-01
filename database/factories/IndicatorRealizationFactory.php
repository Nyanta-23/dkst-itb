<?php

namespace Database\Factories;

use App\Models\IndicatorRealization;
use App\Models\ProgramIndicator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IndicatorRealization>
 */
class IndicatorRealizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'indicator_id' => ProgramIndicator::factory(),
            'period' => now()->year.'-Q1',
            'actual_value' => fake()->randomFloat(2, 1, 50),
            'notes' => fake()->sentence(6),
            'evidence_path' => null,
            'reported_by' => User::factory(),
            'verified_by' => null,
            'status' => 'submitted',
            'verified_at' => null,
            'verification_notes' => null,
        ];
    }
}
