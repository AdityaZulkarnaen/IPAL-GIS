<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'id_user',
        'id_jenis',
        'kegiatan',
        'no_dokumen',
        'tanggal',
        'revisi',
        'sumber',
        'status_bayar',
        'no_order',
        'kode_sampel',
        'catatan'
    ];

    protected $hidden = [];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    public function jenis()
    {
        return $this->hasOne(JenisModel::class, 'id', 'id_jenis');
    }

    public function transaksi_produk()
    {
        return $this->hasMany(TransaksiProdukModel::class, 'id_transaksi', 'id');
    }

    public function status()
    {
        return $this->hasMany(StatusTransaksiProdukModel::class, 'id_status', 'id');
    }
}
