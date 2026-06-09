<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['name' => 'Hindi', 'code' => 'hi'],
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'Tamil', 'code' => 'ta'],
            ['name' => 'Telugu', 'code' => 'te'],
            ['name' => 'Kannada', 'code' => 'kn'],
            ['name' => 'Bengali', 'code' => 'bn'],
            ['name' => 'Marathi', 'code' => 'mr'],
            ['name' => 'Gujarati', 'code' => 'gu'],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(['code' => $language['code']], $language);
        }
    }
}
