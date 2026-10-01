<?php

namespace Database\Factories;

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Disposition>
 */
class DispositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'letter_id' => Letter::factory(),
            'from_user_id' => User::factory(),
            'to_unit_id' => Unit::factory(),
            'to_user_id' => null,
            'instruction' => fake()->sentence(8),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'status' => 'new',
            'read_at' => null,
        ];
    }
}
