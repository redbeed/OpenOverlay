<?php

namespace Redbeed\OpenOverlay\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\TwitchEventReceived;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;

class AddTwitchUserSubscriber implements ShouldQueue
{

    public function handle(TwitchEventReceived $event)
    {
        if ($event->event->event_type !== 'channel.subscribe') {
            return;
        }

        $subscriberData = $event->event->event_data;

        UserSubscriber::firstOrCreate([
            'twitch_user_id' => $event->event->event_user_id,
            'subscriber_user_id' => $subscriberData['user_id'],
            'subscriber_username' => $subscriberData['user_name'],
            'tier' => $subscriberData['user_name'],
            'tier_name' => $subscriberData['plan_name'],
            'is_gift' => $subscriberData['is_gift'],
        ]);
    }
}
