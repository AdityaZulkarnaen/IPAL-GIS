<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class IpalUpload extends Model
{
    protected static ?bool $hasIsActiveColumnCache = null;
    protected static array $activeUploadIdCacheByType = [];

    protected $table = 'ipal_uploads';

    protected $fillable = [
        'user_id',
        'tipe',
        'nama_file_asli',
        'total_fitur',
        'status',
        'is_active',
        'pesan_error',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public static function hasIsActiveColumn(): bool
    {
        if (static::$hasIsActiveColumnCache === null) {
            static::$hasIsActiveColumnCache = Schema::hasColumn('ipal_uploads', 'is_active');
        }

        return static::$hasIsActiveColumnCache;
    }

    public static function activeCompletedUploadId(string $tipe): ?int
    {
        if (array_key_exists($tipe, static::$activeUploadIdCacheByType)) {
            return static::$activeUploadIdCacheByType[$tipe];
        }

        $latestId = static::query()
            ->where('tipe', $tipe)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('id');

        static::$activeUploadIdCacheByType[$tipe] = $latestId !== null ? (int) $latestId : null;

        return static::$activeUploadIdCacheByType[$tipe];
    }

    public function getIsActiveAttribute($value): bool
    {
        if ($this->status !== 'completed' || empty($this->tipe)) {
            return false;
        }

        $activeId = static::activeCompletedUploadId((string) $this->tipe);

        return $activeId !== null && (int) $this->id === $activeId;
    }

    public static function setLatestAsActive(string $tipe): void
    {
        if (!static::hasIsActiveColumn()) {
            return;
        }

        static::where('tipe', $tipe)
            ->where('status', 'completed')
            ->update(['is_active' => false]);

        $latestCompleted = static::where('tipe', $tipe)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if ($latestCompleted !== null) {
            $latestCompleted->update(['is_active' => true]);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manholes()
    {
        return $this->hasMany(IpalManhole::class, 'upload_id');
    }

    public function pipes()
    {
        return $this->hasMany(IpalJaringanPipa::class, 'upload_id');
    }
}
