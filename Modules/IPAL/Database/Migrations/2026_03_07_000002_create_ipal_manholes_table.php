<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipal_manholes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_id')->nullable()->constrained('ipal_uploads')->onDelete('set null');
            $table->string('kode_manhole')->unique();
            $table->string('bentuk')->nullable();
            $table->decimal('dim_mh', 10, 2)->nullable();
            $table->decimal('panjang', 10, 2)->nullable();
            $table->decimal('lebar', 10, 2)->nullable();
            $table->decimal('kedalaman', 10, 2)->nullable();
            $table->string('material_mh')->nullable();
            $table->string('struktur_mh')->nullable();
            $table->string('kondisi_mh')->nullable();
            $table->decimal('sedimen', 10, 2)->nullable();
            $table->decimal('jarak_pipa', 10, 2)->nullable();
            $table->decimal('ukuran_pipa', 10, 2)->nullable();
            $table->string('material_pipa')->nullable();
            $table->string('sekitar')->nullable();
            $table->string('surveyor')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->decimal('ketinggian', 12, 6)->nullable();
            $table->string('topografi')->nullable();
            $table->string('jenis_tanah')->nullable();
            $table->decimal('longitude', 12, 8);
            $table->decimal('latitude', 12, 8);
            $table->json('geometry');
            $table->string('foto_1')->nullable();
            $table->string('foto_2')->nullable();
            $table->string('foto_3')->nullable();
            $table->string('foto_4')->nullable();
            $table->decimal('probabilitas', 8, 2)->nullable();
            $table->decimal('dampak', 8, 2)->nullable();
            $table->decimal('tingkat_risiko', 8, 2)->nullable();
            $table->string('risiko')->nullable();
            $table->string('klasifikasi')->nullable();
            $table->string('pengendali')->nullable();
            $table->integer('sektor')->nullable();
            $table->string('status')->default('aman');
            $table->string('wilayah')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipal_manholes');
    }
};
