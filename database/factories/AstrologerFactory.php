<?php

namespace Database\Factories;

use App\Enums\AstrologerStatus;
use App\Enums\ConsultationMode;
use App\Models\Astrologer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Astrologer>
 */
class AstrologerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->astrologer(),
            'bio' => fake()->paragraph(),
            'years_of_experience' => fake()->numberBetween(1, 25),
            'price_per_minute' => fake()->numberBetween(500, 5000),
            'consultation_modes' => [ConsultationMode::Chat->value],
            'is_online' => false,
            'rating' => fake()->randomFloat(2, 3.0, 5.0),
            'total_reviews' => fake()->numberBetween(0, 500),
            'status' => AstrologerStatus::Applied,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AstrologerStatus::Approved,
            'verified_at' => now(),
        ]);
    }

    public function online(): static
    {
        return $this->approved()->state(fn (array $attributes) => [
            'is_online' => true,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AstrologerStatus::Rejected,
            'verification_notes' => fake()->sentence(),
        ]);
    }

    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AstrologerStatus::PendingVerification,
        ]);
    }
}
