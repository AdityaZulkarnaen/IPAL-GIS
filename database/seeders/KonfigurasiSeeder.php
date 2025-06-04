<?php

namespace Database\Seeders;

use App\Models\KonfigurasiModel;
use Illuminate\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KonfigurasiModel::create([
            'nama_sistem' => 'Nama Sistem Anda',
            'kop_surat' => 'KOP SURAT INSTANSI ANDA',
            'nama_instansi' => 'Nama Instansi/Perusahaan',
            'logo' => 'path/ke/logo.png',
            'kontak' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat No. 123, Kota, Provinsi',
            'maps' => 'https://maps.google.com/...',
            'instagram' => 'https://instagram.com/akun_instagram',
            'youtube' => 'https://youtube.com/akun_youtube',
            'website' => 'https://website-anda.com',
            'petunjuk_penggunaan' => 'Petunjuk penggunaan sistem...'
        ]);

        $this->command->info('Data konfigurasi berhasil ditambahkan!');
    }
}
