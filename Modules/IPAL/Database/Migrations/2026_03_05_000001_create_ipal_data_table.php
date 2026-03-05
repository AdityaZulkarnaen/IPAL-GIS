<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration contoh untuk module IPAL.
 * Gunakan prefix 'ipal_' untuk semua tabel IPAL agar tidak bentrok dengan tabel utama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipal_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('lokasi')->nullable();
            $table->string('kapasitas')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipal_data');
    }
};
