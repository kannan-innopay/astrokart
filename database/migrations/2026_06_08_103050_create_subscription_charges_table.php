<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->foreignId('wallet_transaction_id')->nullable();
            $table->string('status', 20)->default('completed');
            $table->date('charged_for_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_charges');
    }
};
