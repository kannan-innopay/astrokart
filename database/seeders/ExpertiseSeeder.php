<?php

namespace Database\Seeders;

use App\Models\Expertise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpertiseSeeder extends Seeder
{
    public function run(): void
    {
        $expertises = [
            ['name' => 'Vedic Astrology', 'description' => 'Traditional Hindu astrology system based on Vedic scriptures'],
            ['name' => 'Tarot Reading', 'description' => 'Divination using tarot cards for guidance and insight'],
            ['name' => 'Numerology', 'description' => 'Study of numbers and their mystical significance'],
            ['name' => 'Palmistry', 'description' => 'Reading palm lines to predict life events'],
            ['name' => 'Vastu Shastra', 'description' => 'Traditional Indian system of architecture and design'],
            ['name' => 'KP Astrology', 'description' => 'Krishnamurti Paddhati system of stellar astrology'],
            ['name' => 'Prashna Kundali', 'description' => 'Horary astrology based on the time of question'],
            ['name' => 'Horoscope Matching', 'description' => 'Compatibility matching for marriage and relationships'],
        ];

        foreach ($expertises as $expertise) {
            Expertise::firstOrCreate(
                ['slug' => Str::slug($expertise['name'])],
                [
                    'name' => $expertise['name'],
                    'slug' => Str::slug($expertise['name']),
                    'description' => $expertise['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
