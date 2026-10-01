<?php

namespace Database\Factories;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Letter>
 */
class LetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(Letter::TYPES),
            'letter_number' => strtoupper(fake()->bothify('###/DKST/??/2026')),
            'agenda_number' => strtoupper(fake()->bothify('AG-2026-####')),
            'subject' => fake()->sentence(4),
            'sender' => fake()->company(),
            'recipient' => fake()->company(),
            'letter_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'received_date' => null,
            'classification' => fake()->randomElement(Letter::CLASSIFICATIONS),
            'file_path' => 'letters/test/'.fake()->uuid().'.pdf',
            'created_by' => User::factory(),
            'status' => 'new',
        ];
    }
}
