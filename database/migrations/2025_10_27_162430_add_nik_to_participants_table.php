<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_nik_to_participants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // NIK biasanya 16 digit dan harus unik (unik hanya untuk event, tapi kita set unik di sini dulu)
            $table->string('nik', 16)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }
};
