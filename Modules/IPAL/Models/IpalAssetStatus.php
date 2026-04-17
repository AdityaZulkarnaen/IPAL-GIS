<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;

class IpalAssetStatus extends Model
{
    public const ASSET_TYPE_MANHOLE = 'manhole';
    public const ASSET_TYPE_PIPE = 'pipe';

    protected $table = 'ipal_asset_statuses';

    protected $fillable = [
        'asset_type',
        'asset_code',
        'asset_id',
        'status',
    ];

    protected $casts = [
        'asset_id' => 'integer',
    ];

    public static function normalizeStatus(?string $status): string
    {
        $raw = strtolower(trim((string) $status));

        if ($raw === 'aman' || $raw === 'baik') {
            return 'baik';
        }

        if ($raw === 'dalam perbaikan' || $raw === 'perbaikan') {
            return 'perbaikan';
        }

        if ($raw === 'bermasalah' || $raw === 'masalah' || $raw === 'rusak') {
            return 'rusak';
        }

        return 'baik';
    }
}
