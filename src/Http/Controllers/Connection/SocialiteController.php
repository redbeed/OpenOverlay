<?php


namespace Redbeed\OpenOverlay\Http\Controllers\Connection;

use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Url\Url;
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
        $callbackUrl = $this->callbackUrl();

        if (!empty($callbackUrl)) {

            /** @var RedirectResponse $redirect */
            $redirect = $this->socialite()->redirect();


            $redirectUrl = Url::fromString($redirect->getTargetUrl());
            $redirectUrl = $redirectUrl->withQueryParameter('redirect_uri', $callbackUrl);

            $redirect->setTargetUrl(str_replace('%2B', '+', (string) $redirectUrl));

            return $redirect;
        }

        return $this->socialite()->redirect();
    }

    protected function callbackUrl(): string
    {
        return '';
    }
}
