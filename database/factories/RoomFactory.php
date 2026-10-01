<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'building' => fake()->buildingNumber(),
            'capacity' => fake()->numberBetween(4, 100),
            'facilities' => 'Proyektor, AC, Whiteboard',
            'is_active' => true,
        ];
    }
}
