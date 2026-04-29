<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function scopeFromActiveUpload($query)
    {
        $activeUploadId = IpalUpload::activeCompletedUploadId('pipe');

        if ($activeUploadId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('upload_id', $activeUploadId);
    }

    public function canonicalStatus(): HasOne
    {
        return $this->hasOne(IpalAssetStatus::class, 'asset_code', 'kode_pipa')
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_PIPE);
    }

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class, 'pipa_id');
    }
}
