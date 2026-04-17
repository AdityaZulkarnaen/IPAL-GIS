<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IpalManhole extends Model
{
    protected $table = 'ipal_manholes';

    protected $fillable = [
        'upload_id',
        'kode_manhole',
        'bentuk',
        'dim_mh',
        'panjang',
        'lebar',
        'kedalaman',
        'material_mh',
        'struktur_mh',
        'kondisi_mh',
        'sedimen',
        'jarak_pipa',
        'ukuran_pipa',
        'material_pipa',
        'sekitar',
        'surveyor',
        'desa',
        'kecamatan',
        'ketinggian',
        'topografi',
        'jenis_tanah',
        'longitude',
        'latitude',
        'geometry',
        'foto_1',
        'foto_2',
        'foto_3',
        'foto_4',
        'probabilitas',
        'dampak',
        'tingkat_risiko',
        'risiko',
        'klasifikasi',
        'pengendali',
        'sektor',
        'status',
        'wilayah',
    ];

    protected $casts = [
        'geometry' => 'array',
        'dim_mh' => 'decimal:2',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'kedalaman' => 'decimal:2',
        'sedimen' => 'decimal:2',
        'jarak_pipa' => 'decimal:2',
        'ukuran_pipa' => 'decimal:2',
        'ketinggian' => 'decimal:6',
        'longitude' => 'decimal:8',
        'latitude' => 'decimal:8',
        'probabilitas' => 'decimal:2',
        'dampak' => 'decimal:2',
        'tingkat_risiko' => 'decimal:2',
        'sektor' => 'integer',
    ];

    public function upload()
    {
        return $this->belongsTo(IpalUpload::class, 'upload_id');
    }

    public function logs()
    {
        return $this->hasMany(IpalManholeLog::class, 'manhole_id');
    }

    public function canonicalStatus(): HasOne
    {
        return $this->hasOne(IpalAssetStatus::class, 'asset_code', 'kode_manhole')
            ->where('asset_type', IpalAssetStatus::ASSET_TYPE_MANHOLE);
    }

    public function scopeFromActiveUpload($query)
    {
        $activeUploadId = IpalUpload::activeCompletedUploadId('manhole');

        if ($activeUploadId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('upload_id', $activeUploadId);
    }

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class, 'manhole_id');
    }
}
