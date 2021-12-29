<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch;

use Carbon\Carbon;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Models\Twitch\UserFollowers;

class NewFollowerListener extends EventListener
{

    protected function eventType(): string
    {
        return 'channel.follow';
    }

    public function handleEvent(EventReceived $event): void
    {
        $followerData = $event->event->event_data;

        UserFollowers::firstOrCreate(
            [
                'twitch_user_id'   => $event->event->event_user_id,
                'follower_user_id' => $followerData['user_id'],
            ], [
                'follower_username' => $followerData['user_name'],
                'followed_at'       => Carbon::parse($event->event->event_sent),
            ]
        );
    }
}
