<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdmin(
            'Super Admin',
            'superadmin@diaspay.in',
            '9000000001',
            UserRole::SuperAdmin,
            (string) config('app.seed.super_admin_password'),
        );

        $this->createAdmin(
            'Admin User',
            'admin@diaspay.in
            ',
            '9000000002',
            UserRole::Admin,
            (string) config('app.seed.admin_password'),
        );
    }

    private function createAdmin(string $name, string $email, string $mobile, UserRole $role, string $password): void
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'mobile' => $mobile,
                'password' => $password,
                'role' => $role,
                'account_status' => AccountStatus::Active,
                'mobile_verified_at' => now(),
                'email_verified_at' => now(),
                'preferred_language' => 'en',
            ],
        );

        Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'currency' => 'INR']);
    }
}
