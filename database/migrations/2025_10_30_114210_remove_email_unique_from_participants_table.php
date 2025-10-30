<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Hapus index unik yang dibuat di migrasi 2025_10_23_065528
            $table->dropUnique(['email']);
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Jika di rollback, kembalikan batasan unik (seperti kondisi awal)
            $table->string('email')->unique()->change();
        });
    }
};
