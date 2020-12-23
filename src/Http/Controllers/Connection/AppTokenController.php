<?php

namespace Redbeed\OpenOverlay\Http\Controllers\Connection;

class AppTokenController extends SocialiteController
{

    protected $socialiteDriver = 'twitch_client_credentials';

    public function __construct()
    {
        $generateAllowed = config('openoverlay.webhook.twitch.app_token.regenerate');

        if ($generateAllowed !== true) {
            abort(404);
        }
    }

    protected function callbackUrl(): string
    {
        return route('open_overlay.connection.app-token.callback');
    }

    public function handleProviderCallback()
    {
        $auth = $this->socialite()->getAccessTokenResponse(request()->get('code'));

        if (empty($auth['access_token'])) {
            return abort(404, 'access_token not found in body');
        }

        return $auth['access_token'];
    }
}
