<?php

namespace Database\Seeders;

use App\Enums\AstrologerStatus;
use App\Enums\ConsultationMode;
use App\Models\Astrologer;
use App\Models\Expertise;
use App\Models\Language;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@astrokart.com',
            'mobile' => '9000000001',
        ]);
        Wallet::factory()->for($superAdmin)->create(['balance' => 0]);

        // Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@astrokart.com',
            'mobile' => '9000000002',
        ]);
        Wallet::factory()->for($admin)->create(['balance' => 0]);

        // Astrologers
        $expertises = Expertise::all();
        $languages = Language::all();

        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->astrologer()->create();
            Wallet::factory()->for($user)->create();

            $astrologer = Astrologer::factory()->approved()->create([
                'user_id' => $user->id,
                'consultation_modes' => [ConsultationMode::Chat->value],
                'is_online' => $i < 3,
            ]);

            $astrologer->expertises()->attach(
                $expertises->random(fake()->numberBetween(1, 3))->pluck('id')
            );
            $astrologer->languages()->attach(
                $languages->random(fake()->numberBetween(1, 3))->pluck('id')
            );
        }

        // Customers
        User::factory(10)->create()->each(function (User $user) {
            Wallet::factory()->for($user)->create();
        });
    }
}
