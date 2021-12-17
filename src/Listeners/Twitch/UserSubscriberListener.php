<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch;

use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;

class UserSubscriberListener implements ShouldQueue
{
    public function handle(EventReceived $event)
    {
        if ($event->event->event_type !== 'channel.subscribe') {
            return;
        }

        $subscriberData = $event->event->event_data;

        UserSubscriber::firstOrCreate([
            'twitch_user_id' => $event->event->event_user_id,
            'subscriber_user_id' => $subscriberData['user_id'],
        ], [
            'subscriber_username' => $subscriberData['user_name'],
            'tier' => $subscriberData['user_name'],
            'tier_name' => $subscriberData['plan_name'],
            'is_gift' => $subscriberData['is_gift'],
        ]);
    }
}
