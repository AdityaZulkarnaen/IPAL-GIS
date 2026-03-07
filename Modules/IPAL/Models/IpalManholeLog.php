<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;

class IpalManholeLog extends Model
{
    protected $table = 'ipal_manhole_logs';

    protected $fillable = [
        'manhole_id',
        'waktu',
        'kegiatan',
        'lokasi',
        'catatan',
        'pekerjaan',
        'foto_1',
        'foto_2',
        'foto_3',
        'foto_4',
        'alat_bahan',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'alat_bahan' => 'array',
    ];

    public function manhole()
    {
        return $this->belongsTo(IpalManhole::class, 'manhole_id');
    }
}
