<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Redbeed\OpenOverlay\Events\Twitch\BotTokenExpires;
use Redbeed\OpenOverlay\Service\Twitch\AuthClient;

class UpdateBotToken
{
    public function handle(BotTokenExpires $event)
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

            return;
        }

        $event->botModel->service_token = $response['access_token'];
        $event->botModel->service_refresh_token = $response['refresh_token'];
        $event->botModel->expires_at = Carbon::now()->addSeconds($response['expires_in']);
        $event->botModel->save();
    }
}
