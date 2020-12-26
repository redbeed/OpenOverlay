<?php

namespace Redbeed\OpenOverlay\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\TwitchEventReceived;
use Redbeed\OpenOverlay\Models\Twitch\UserFollowers;

class AddTwitchUserFollower implements ShouldQueue
{

    public function handle(TwitchEventReceived $event)
    {
        if ($event->event->event_type !== 'channel.follow') {
            return;
        }

        $followerData = $event->event->event_data;

        UserFollowers::firstOrCreate([
            'twitch_user_id' => $event->event->event_user_id,
            'follower_user_id' => $followerData['user_id'],
        ], [
            'follower_username' => $followerData['user_name'],
            'followed_at' => Carbon::parse($event->event->event_sent),
        ]);
    }
}
