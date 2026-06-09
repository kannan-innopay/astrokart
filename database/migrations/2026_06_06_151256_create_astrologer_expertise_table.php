<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astrologer_expertise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expertise_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['astrologer_id', 'expertise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_expertise');
    }
};
