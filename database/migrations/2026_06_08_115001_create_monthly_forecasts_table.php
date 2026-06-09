<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('forecast_month');
            $table->json('forecast_json');
            $table->string('engine_version', 50);
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->unique(['user_id', 'forecast_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_forecasts');
    }
};
