<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = [
        'users', 'astrologers', 'expertises', 'languages', 'wallets',
        'consultations', 'chat_messages', 'wallet_transactions', 'payments',
    ];

    public function up(): void
    {
        // Step 1: Add nullable uuid column
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });
        }

        // Step 2: Backfill existing rows
        foreach ($this->tables as $tableName) {
            $rows = DB::table($tableName)->whereNull('uuid')->get(['id']);
            foreach ($rows as $row) {
                DB::table($tableName)
                    ->where('id', $row->id)
                    ->update(['uuid' => Str::uuid7()->toString()]);
            }
        }

        // Step 3: Add unique index (SQLite doesn't support altering column to NOT NULL easily)
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unique('uuid');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $tbl) use ($table) {
                $tbl->dropUnique([$table === 'wallet_transactions' ? 'uuid' : 'uuid']);
                $tbl->dropColumn('uuid');
            });
        }
    }
};
