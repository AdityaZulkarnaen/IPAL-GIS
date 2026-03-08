<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aduan_id')->constrained('aduan')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_sebelumnya');
            $table->string('status_sesudah');
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan_history');
    }
};
