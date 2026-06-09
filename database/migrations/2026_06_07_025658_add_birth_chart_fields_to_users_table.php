<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('birth_chart')->nullable()->after('account_status');
            $table->decimal('birth_latitude', 10, 7)->nullable()->after('birth_chart');
            $table->decimal('birth_longitude', 10, 7)->nullable()->after('birth_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['birth_chart', 'birth_latitude', 'birth_longitude']);
        });
    }
};
