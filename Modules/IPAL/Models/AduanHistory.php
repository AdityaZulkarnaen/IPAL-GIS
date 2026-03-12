<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class AduanHistory extends Model
{
    public $timestamps = false;

    protected $table = 'aduan_history';

    protected $fillable = [
        'aduan_id',
        'admin_id',
        'status_sebelumnya',
        'status_sesudah',
        'catatan_tindak_lanjut',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function aduan(): BelongsTo
    {
        return $this->belongsTo(Aduan::class, 'aduan_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
