<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch;

use Carbon\Carbon;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Models\Twitch\UserFollowers;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;

class NewFollowerListener extends EventListener
{

    protected function eventType(): string
    {
        return 'channel.follow';
    }

    public function handleEvent(EventReceived $event): void
    {
        $followerData = $event->event->event_data;

        $followerModal = UserFollowers::withTrashed()
            ->where('twitch_user_id', $event->event->event_user_id)
            ->where('follower_user_id', $followerData['user_id'])
            ->first();

        if (empty($followerModal)) {
            UserFollowers::create([
                'twitch_user_id'    => $event->event->event_user_id,
                'follower_user_id'  => $followerData['user_id'],
                'follower_username' => $followerData['user_name'],
                'followed_at'       => Carbon::parse($followerData['followed_at']),
                'deleted_at'        => null,
            ]);

            return;
        }

        $followerModal->follower_username = $followerData['user_name'];
        $followerModal->followed_at = Carbon::parse($followerData['followed_at']);

        if ($followerModal->trashed()) {
            $followerModal->restore();
        }

        $followerModal->save();
    }
}
