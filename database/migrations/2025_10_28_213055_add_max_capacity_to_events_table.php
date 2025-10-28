<?php
// File: database/migrations/2025_10_28_xxxxxx_add_max_capacity_to_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Kolom ini unsigned integer (bilangan bulat positif) dan nullable (opsional/tidak terbatas)
            $table->unsignedInteger('max_capacity')->nullable()->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('max_capacity');
        });
    }
};
