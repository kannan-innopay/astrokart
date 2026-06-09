<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('prediction_date');
            $table->json('prediction_json');
            $table->string('engine_version', 50);
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->unique(['user_id', 'prediction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_predictions');
    }
};
