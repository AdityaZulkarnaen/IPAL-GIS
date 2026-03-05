<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model contoh untuk module IPAL.
 * Developer IPAL bisa menambah/mengedit model sesuai kebutuhan.
 * 
 * Contoh tabel: ipal_data (gunakan prefix ipal_ untuk semua tabel IPAL)
 */
class IpalModel extends Model
{
    use HasFactory;

    protected $table = 'ipal_data';

    protected $fillable = [
        'nama',
        'lokasi',
        'kapasitas',
        'status',
        'keterangan',
    ];

    protected $hidden = [];
}
