<?php

namespace Redbeed\OpenOverlay\Http\Controllers\Connection;

use Carbon\Carbon;
use Redbeed\OpenOverlay\Models\BotConnection;

class BotAuthController extends SocialiteController
{
    protected function callbackUrl(): string
    {
        return route('open_overlay.connection.bot.callback');
    }

    public function handleProviderCallback()
    {
        $botUser = $this->socialite()->user();

        if (empty($botUser->token)) {
            return redirect()->route('dashboard');
        }

        BotConnection::updateOrCreate(
            [
                'service' => 'twitch',
                'bot_user_id' => $botUser->getId(),
            ],
            [
                'bot_username' => $botUser->getName(),
                'service_token' => $botUser->token,
                'service_refresh_token' => $botUser->refreshToken,
                'expires_at' => Carbon::now()->addSeconds($botUser->expiresIn),
            ]
        );

        return redirect()->route('dashboard');
    }
}
