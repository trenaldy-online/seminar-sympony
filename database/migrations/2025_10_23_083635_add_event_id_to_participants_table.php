<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Menambahkan kolom foreign key event_id
            $table->foreignId('event_id')
                  ->nullable() // Memungkinkan peserta lama yang sudah ada tidak memiliki event_id
                  ->constrained() // Membuat foreign key ke tabel 'events'
                  ->onDelete('cascade') // Jika event dihapus, data peserta terkait juga dihapus
                  ->after('id'); // Menempatkan kolom ini setelah kolom 'id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Hapus foreign key sebelum menghapus kolom itu sendiri
            $table->dropConstrainedForeignId('event_id');
        });
    }
};
