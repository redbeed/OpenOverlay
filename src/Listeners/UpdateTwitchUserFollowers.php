<?php

namespace Redbeed\OpenOverlay\Listeners;

use Carbon\Carbon;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Models\Twitch\UserFollowers;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;

class UpdateTwitchUserFollowers implements ShouldQueue
{
    public function handle(UserConnectionChanged $event)
    {
        $twitchConnection = $event->user->connections()->where('service', 'twitch')->first();

        $followerList = UsersClient::withAppToken($twitchConnection->service_token)
            ->withOptions([
                RequestOptions::QUERY => [
                    'first' => 1,
                ],
            ])
            ->allFollowers($twitchConnection->service_user_id);

        foreach ($followerList['data'] as $followerData) {
            UserFollowers::firstOrCreate([
                'twitch_user_id' => $twitchConnection->service_user_id,
                'follower_user_id' => $followerData['from_id'],
                'follower_username' => $followerData['from_name'],
                'followed_at' => Carbon::parse($followerData['followed_at']),
            ]);
        }
    }

}
