<?php

namespace Modules\IPAL\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class IpalUpload extends Model
{
    protected $table = 'ipal_uploads';

    protected $fillable = [
        'user_id',
        'tipe',
        'nama_file_asli',
        'total_fitur',
        'status',
        'pesan_error',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

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
