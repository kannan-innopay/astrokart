<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/cities.csv');

        if (! file_exists($path)) {
            $this->command->error('cities.csv not found at database/data/cities.csv');

            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $batch = [];
        $count = 0;
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $batch[] = [
                'name' => $data['name'],
                'state_name' => $data['state_name'],
                'country_code' => $data['country_code'],
                'latitude' => (float) $data['latitude'],
                'longitude' => (float) $data['longitude'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= 500) {
                City::insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            City::insert($batch);
            $count += count($batch);
        }

        fclose($handle);

        $this->command->info("Seeded {$count} cities.");
    }
}
