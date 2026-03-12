<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AduanDokumentasi extends Model
{
    public $timestamps = false;

    protected $table = 'aduan_dokumentasi';

    protected $fillable = [
        'aduan_id',
        'file_name',
        'file_path',
        'tipe_pengunggah',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    protected $appends = ['url'];

    public function aduan(): BelongsTo
    {
        return $this->belongsTo(Aduan::class, 'aduan_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
