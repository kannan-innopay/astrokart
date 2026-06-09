<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compatibility_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('partner_name')->nullable();
            $table->date('partner_dob')->nullable();
            $table->unsignedTinyInteger('partner_moon_nakshatra');
            $table->unsignedTinyInteger('partner_moon_rashi');
            $table->unsignedTinyInteger('score');
            $table->json('result_json');
            $table->unsignedInteger('amount_charged')->default(0);
            $table->foreignId('wallet_transaction_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compatibility_reports');
    }
};
