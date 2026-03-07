<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipal_manhole_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manhole_id')->constrained('ipal_manholes')->onDelete('cascade');
            $table->datetime('waktu')->nullable();
            $table->text('kegiatan')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('catatan')->nullable();
            $table->text('pekerjaan')->nullable();
            $table->string('foto_1')->nullable();
            $table->string('foto_2')->nullable();
            $table->string('foto_3')->nullable();
            $table->string('foto_4')->nullable();
            $table->json('alat_bahan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipal_manhole_logs');
    }
};
