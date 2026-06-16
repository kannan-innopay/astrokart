<?php

namespace Database\Factories;

use App\Models\Astrologer;
use App\Models\AstrologerPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AstrologerPhoto>
 */
class AstrologerPhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'astrologer_id' => Astrologer::factory(),
            'file_path' => 'astrologer-photos/'.fake()->uuid().'.jpg',
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
