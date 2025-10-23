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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Event (ex: Seminar Web Dev 2024)
            $table->string('slug')->unique(); // Slug untuk URL yang bersih
            $table->text('description')->nullable();
            $table->date('date'); // Tanggal pelaksanaan Event
            $table->boolean('is_active')->default(true); // Status apakah event masih dibuka pendaftarannya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
