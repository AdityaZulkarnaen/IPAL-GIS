<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipal_jaringan_pipa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_id')->nullable()->constrained('ipal_uploads')->onDelete('set null');
            $table->string('id_jalur')->nullable();
            $table->string('kode_pipa')->unique();
            $table->decimal('pipe_dia', 10, 2)->nullable();
            $table->string('fungsi')->nullable();
            $table->decimal('length_km', 12, 8)->nullable();
            $table->integer('tahun')->nullable();
            $table->string('source')->nullable();
            $table->string('material')->nullable();
            $table->json('geometry');
            $table->string('status')->default('aman');
            $table->string('wilayah')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipal_jaringan_pipa');
    }
};
