<?php

namespace Redbeed\OpenOverlay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Redbeed\OpenOverlay\OpenOverlay;

class BotConnection extends Model
{
    use HasFactory;

    protected $table = 'bots_connections';

    protected $fillable = [
        'service', 'bot_user_id', 'bot_username',
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

    private function decryptString(string $key): string {
        return Crypt::decryptString($key);
    }

    private function encryptString(string $value): string {
        return Crypt::encryptString($value);
    }

    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'users_bots_enabled', 'bot_id');
    }
}
