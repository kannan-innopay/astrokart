<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_analyses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('birth_chart_hash', 64);
            $table->string('engine_version', 50);
            $table->string('analysis_type', 20)->default('full');
            $table->json('analysis_json');
            $table->json('free_summary_json');
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['user_id', 'birth_chart_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_analyses');
    }
};
