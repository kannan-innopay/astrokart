<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astrologer_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['astrologer_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_language');
    }
};
