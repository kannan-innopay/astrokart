<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('astrologer_id')->constrained()->cascadeOnDelete();
            $table->string('consultation_type')->default('chat');
            $table->string('status')->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedInteger('price_per_minute')->default(0);
            $table->unsignedBigInteger('gross_amount')->default(0);
            $table->unsignedBigInteger('platform_commission')->default(0);
            $table->unsignedBigInteger('astrologer_earning')->default(0);
            $table->unsignedTinyInteger('commission_rate')->default(30);
            $table->string('ended_by')->nullable();
            $table->string('end_reason')->nullable();
            $table->timestamp('rated_at')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('review')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['astrologer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
