<?php


namespace Redbeed\OpenOverlay\Http\Controllers\Connection;

use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class SocialiteController
{
    protected $socialiteDriver = 'twitch';

    protected function socialite(): Provider
    {
        return Socialite::driver($this->socialiteDriver)
            ->setScopes(config('openoverlay.service.twitch.scopes'));
    }

    public function redirect(): RedirectResponse
    {
        return $this->socialite()->redirect();
    }
}
