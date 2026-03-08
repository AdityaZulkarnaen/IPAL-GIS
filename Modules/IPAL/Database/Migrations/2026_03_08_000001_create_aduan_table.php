<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->foreignId('id_pelapor')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pipa_id')->nullable()->constrained('ipal_jaringan_pipa')->nullOnDelete();
            $table->foreignId('manhole_id')->nullable()->constrained('ipal_manholes')->nullOnDelete();
            $table->text('deskripsi');
            $table->json('titik_koordinat')->nullable();
            $table->enum('status_aduan', ['masuk', 'verifikasi', 'proses', 'selesai'])->default('masuk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};
