<?php

namespace Redbeed\OpenOverlay\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Redbeed\OpenOverlay\Events\TwitchBotTokenExpires;
use Redbeed\OpenOverlay\Service\Twitch\AuthClient;

class UpdateTwitchBotToken
{
    public function handle(TwitchBotTokenExpires $event)
    {
        if ($event->botModel->service !== 'twitch' || empty($event->botModel->service_refresh_token)) {
            return;
        }

        try {
            /** @var AuthClient $client */
            $client = AuthClient::http();
            $response = $client->refreshToken($event->botModel->service_refresh_token);
        } catch (\Exception $exception) {
            Log::error("Bot Connection deleted");
            Log::error($exception);

            //$event->botModel->delete();

            return;
        }

        $event->botModel->service_token = $response['access_token'];
        $event->botModel->service_refresh_token = $response['refresh_token'];
        $event->botModel->expires_at = Carbon::now()->addSeconds($response['expires_in']);
        $event->botModel->save();
    }
}
