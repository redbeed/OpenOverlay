<?php

namespace Redbeed\OpenOverlay\Models\User;

use Redbeed\OpenOverlay\Models\BotConnection;

trait UserOpenOverlay
{
    public function connections()
    {
        return $this->hasMany(Connection::class);
    }

    public function bots()
    {
        return $this->belongsToMany(BotConnection::class, 'users_bots_enabled', 'user_id', 'bot_id');
    }
}
