<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Aduan extends Model
{
    protected $table = 'aduan';

    protected $fillable = [
        'nomor_tiket',
        'id_pelapor',
        'pipa_id',
        'manhole_id',
        'deskripsi',
        'titik_koordinat',
        'status_aduan',
    ];

    protected $casts = [
        'titik_koordinat' => 'array',
    ];

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pelapor');
    }

    public function pipa(): BelongsTo
    {
        return $this->belongsTo(IpalJaringanPipa::class, 'pipa_id');
    }

    public function manhole(): BelongsTo
    {
        return $this->belongsTo(IpalManhole::class, 'manhole_id');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(AduanDokumentasi::class, 'aduan_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(AduanHistory::class, 'aduan_id')->orderByDesc('created_at');
    }

    public static function generateNomorTiket(): string
    {
        $prefix = 'ADU-' . now()->format('Ymd') . '-';
        $last = static::where('nomor_tiket', 'like', $prefix . '%')
            ->orderByDesc('nomor_tiket')
            ->value('nomor_tiket');

        $sequence = $last ? ((int) substr($last, -5)) + 1 : 1;

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public static function buildAssetGroupKey(?int $pipaId, ?int $manholeId): ?string
    {
        if ($pipaId !== null) {
            return 'pipa:' . $pipaId;
        }

        if ($manholeId !== null) {
            return 'manhole:' . $manholeId;
        }

        return null;
    }

    public function scopeSameAssetAs(Builder $query, self $aduan): Builder
    {
        if ($aduan->pipa_id !== null) {
            return $query->where('pipa_id', $aduan->pipa_id);
        }

        if ($aduan->manhole_id !== null) {
            return $query->where('manhole_id', $aduan->manhole_id);
        }

        return $query->whereKey($aduan->id);
    }
}
