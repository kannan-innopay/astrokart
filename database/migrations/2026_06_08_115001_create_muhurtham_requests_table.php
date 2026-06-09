<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muhurtham_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('purpose', 50);
            $table->date('date_range_start');
            $table->date('date_range_end');
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('amount_charged')->default(0);
            $table->foreignId('wallet_transaction_id')->nullable();
            $table->json('result_json')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muhurtham_requests');
    }
};
