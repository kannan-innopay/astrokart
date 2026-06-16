<?php

namespace Database\Seeders;

use App\Enums\ConsultationMode;
use App\Models\Astrologer;
use App\Models\Expertise;
use App\Models\Language;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@astrokart.com',
            'mobile' => '9000000001',
            'password' => Hash::make(config('app.seed.super_admin_password')),
        ]);
        Wallet::factory()->for($superAdmin)->create(['balance' => 0]);

        // Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@astrokart.com',
            'mobile' => '9000000002',
            'password' => Hash::make(config('app.seed.admin_password')),
        ]);
    }
}
