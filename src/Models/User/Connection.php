<?php

namespace Redbeed\OpenOverlay\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Redbeed\OpenOverlay\OpenOverlay;

class Connection extends Model
{
    use HasFactory;

    protected $table = 'users_connections';

    protected $fillable = [
        'user_id',
        'service', 'service_user_id', 'service_username',
        'service_token', 'service_refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function getServiceTokenAttribute($key): string
    {
        return $this->decryptString($key);
    }

    public function setServiceTokenAttribute($value): void
    {
        $this->attributes['service_token'] = $this->encryptString($value);
    }

    public function getServiceRefreshTokenAttribute($key): string
    {
        return $this->decryptString($key);
    }

    public function setServiceRefreshTokenAttribute($value): void
    {
        $this->attributes['service_refresh_token'] = $this->encryptString($value);
    }

    private function decryptString(string $key): string
    {
        return Crypt::decryptString($key);
    }

    private function encryptString(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function user()
    {
        return $this->belongsTo(OpenOverlay::userModel());
    }
}
