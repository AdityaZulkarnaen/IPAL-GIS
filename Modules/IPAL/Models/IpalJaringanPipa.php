<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;

class IpalJaringanPipa extends Model
{
    protected $table = 'ipal_jaringan_pipa';

    protected $fillable = [
        'upload_id',
        'id_jalur',
        'kode_pipa',
        'pipe_dia',
        'fungsi',
        'length_km',
        'tahun',
        'source',
        'material',
        'geometry',
        'status',
        'wilayah',
    ];

    protected $casts = [
        'geometry' => 'array',
        'pipe_dia' => 'decimal:2',
        'length_km' => 'decimal:8',
        'tahun' => 'integer',
    ];

    public function upload()
    {
        return $this->belongsTo(IpalUpload::class, 'upload_id');
    }
}
