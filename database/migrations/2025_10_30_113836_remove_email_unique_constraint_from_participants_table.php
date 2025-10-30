<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // 1. Hapus index unik yang lama
            $table->dropUnique(['email']);

            // 2. Jika Anda ingin menguji duplikasi di Controller,
            // Anda tidak perlu menambahkan index baru di sini.
            // Jika Anda ingin mengizinkan email yang sama mendaftar ke event berbeda,
            // maka biarkan kolom email tanpa index unik.
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Kembalikan index unik (hanya jika Anda yakin ingin mengembalikannya)
            $table->unique('email');
        });
    }
};
