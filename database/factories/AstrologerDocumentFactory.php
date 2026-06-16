<?php

namespace Database\Factories;

use App\Enums\AstrologerDocumentType;
use App\Models\Astrologer;
use App\Models\AstrologerDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AstrologerDocument>
 */
class AstrologerDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'astrologer_id' => Astrologer::factory(),
            'document_type' => fake()->randomElement(AstrologerDocumentType::cases()),
            'document_number' => fake()->bothify('??########'),
            'file_path' => 'astrologer-documents/'.fake()->uuid().'.pdf',
            'is_verified' => false,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }
}
