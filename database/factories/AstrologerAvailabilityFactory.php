<?php

namespace Database\Factories;

use App\Models\Astrologer;
use App\Models\AstrologerAvailability;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AstrologerAvailability>
 */
class AstrologerAvailabilityFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(6, 18);

        return [
            'astrologer_id' => Astrologer::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $startHour + fake()->numberBetween(2, 6)),
            'is_active' => true,
        ];
    }
}
