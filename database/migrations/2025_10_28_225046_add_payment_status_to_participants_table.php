<?php
// File: database/migrations/2025_10_28_xxxxxx_add_payment_status_to_participants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Status: 0=Menunggu Pembayaran, 1=Pembayaran Tervalidasi (Lunas)
            // Defaultnya adalah 1 (Lunas) agar Event GRATIS otomatis lunas.
            $table->boolean('is_paid')->default(true)->after('is_checked_in');

            // Kode unik untuk mempermudah validasi pembayaran
            $table->unsignedSmallInteger('unique_code')->nullable()->after('is_paid');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'unique_code']);
        });
    }
};
