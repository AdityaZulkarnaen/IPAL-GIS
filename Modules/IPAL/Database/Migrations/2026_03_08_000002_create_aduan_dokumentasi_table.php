<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan_dokumentasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aduan_id')->constrained('aduan')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('tipe_pengunggah', ['pelapor', 'admin'])->default('pelapor');
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan_dokumentasi');
    }
};
