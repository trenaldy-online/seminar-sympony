<?php
// File: database/migrations/2025_10_28_xxxxxx_add_payment_fields_to_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Jika is_paid = 0, event gratis. Jika 1, berbayar. Default: GRATIS.
            $table->boolean('is_paid')->default(false)->after('max_capacity');

            // Detail pembayaran (hanya diisi jika is_paid = true)
            $table->decimal('price', 10, 0)->nullable()->after('is_paid');
            $table->string('bank_name')->nullable()->after('price');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('account_holder')->nullable()->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'price', 'bank_name', 'account_number', 'account_holder']);
        });
    }
};
