<?php

namespace Redbeed\OpenOverlay;

class OpenOverlay
{
    public static function userModel(): string
    {
        return config('auth.providers.users.model');
    }

    /**
     * @return \App\Models\User|mixed
     */
    public static function newUserModel()
    {
        $model = static::userModel();

        return new $model;
    }
}
