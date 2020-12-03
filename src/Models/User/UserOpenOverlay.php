<?php

namespace Redbeed\OpenOverlay\Models\User;

trait UserOpenOverlay
{
    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
}
