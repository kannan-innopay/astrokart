<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astrologer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_documents');
    }
};
