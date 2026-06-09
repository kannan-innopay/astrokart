<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->unique()->after('name');
            $table->timestamp('mobile_verified_at')->nullable()->after('mobile');
            $table->string('role')->default('customer')->after('mobile_verified_at');
            $table->string('gender')->nullable()->after('role');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('time_of_birth')->nullable()->after('date_of_birth');
            $table->string('place_of_birth')->nullable()->after('time_of_birth');
            $table->string('preferred_language')->default('en')->after('place_of_birth');
            $table->string('account_status')->default('active')->after('preferred_language');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mobile',
                'mobile_verified_at',
                'role',
                'gender',
                'date_of_birth',
                'time_of_birth',
                'place_of_birth',
                'preferred_language',
                'account_status',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
