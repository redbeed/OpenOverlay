<?php

namespace Redbeed\OpenOverlay\Listeners;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\SendMessageCommand;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\ChannelsClient;

class AutoShoutOutRaid implements ShouldQueue
{
    public function handle(EventReceived $event)
    {
        if ($event->event->event_type !== 'channel.raid') {
            return;
        }

        /** @var Connection $connection */
        $connection = Connection::where('service_user_id', $event->event->event_user_id)
            ->first();

        if (!$connection) {
            return;
        }

        $chatMessage = config(
            'openoverlay.modules' . AutoShoutOutRaid::class . 'message',
            'Follow :username over at :twitchUrl. They were last playing :gameName'
        );

        $eventData = $event->event->event_data;
        $gameName = '';

        try {
            $channelClient = new ChannelsClient();
            $channels = $channelClient->get($eventData['from_broadcaster_user_id']);
            $channel = head($channels['data']);

            if (!empty($channel['game_id'])) {
                $gameName = $channel['game_name'];
            }
        } catch (ClientException $exception) {
            Log::debug($exception);
            // ignore
        }

        Artisan::call(SendMessageCommand::class, [
            'userId' => $connection->user->id,
            'message' => __($chatMessage, [
                'username' => $eventData['from_broadcaster_user_name'],
                'twitchUrl' => 'https://www.twitch.tv/' . $eventData['from_broadcaster_user_login'],
                'gameName' => $gameName,
            ]),
        ]);
    }
}
