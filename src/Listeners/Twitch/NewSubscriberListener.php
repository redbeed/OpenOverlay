<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch;

use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;

class NewSubscriberListener extends EventListener
{
    protected function eventType(): string
    {
        return 'channel.subscribe';
    }

    public function handleEvent(EventReceived $event): void
    {
        $subscriberData = $event->event->event_data;

        UserSubscriber::firstOrCreate([
            'twitch_user_id'     => $event->event->event_user_id,
            'subscriber_user_id' => $subscriberData['user_id'],
        ], [
            'subscriber_username' => $subscriberData['user_name'],
            'tier'                => $subscriberData['user_name'],
            'tier_name'           => $subscriberData['plan_name'] ?? '',
            'is_gift'             => $subscriberData['is_gift'],
        ]);
    }
}
