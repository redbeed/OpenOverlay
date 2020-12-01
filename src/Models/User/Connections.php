<?php

namespace Redbeed\OpenOverlay\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Redbeed\OpenOverlay\OpenOverlay;

class Connections extends Model
{
    use HasFactory;

    protected $table = 'users_connections';

    protected $fillable = [
        'user_id',
        'service',
        'service_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function getServiceTokenAttribute($key): string
    {
        return Crypt::decryptString($key);
    }

    public function setServiceTokenAttribute($value): void
    {
        $this->attributes['service_token'] = Crypt::encryptString($value);
    }

    public function user()
    {
        return $this->belongsTo(OpenOverlay::userModel());
    }
}
