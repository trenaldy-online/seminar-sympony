<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Hapus index unik pada email
            // Index bernama 'participants_email_unique' dibuat di migrasi awal Anda
            $table->dropUnique(['email']);

            // Hapus index unik pada nik (jika ada, diasumsikan ada)
            // Jika kolom nik disetel unique() di migrasi 2025_10_27_162430
            if (Schema::hasColumn('participants', 'nik')) {
                // MySQL convention: nama_tabel_nama_kolom_unique
                $table->dropUnique(['nik']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Jika di rollback, kembalikan batasan unik.
            $table->string('email')->unique()->change();
            if (Schema::hasColumn('participants', 'nik')) {
                $table->string('nik')->unique()->nullable(false)->change(); // Ubah sesuai definisi awal NIK
            }
        });
    }
};
