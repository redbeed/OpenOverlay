<?php

namespace Redbeed\OpenOverlay\Listeners;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Models\Twitch\UserFollowers;
use Redbeed\OpenOverlay\Service\Twitch\ApiClient;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;
use Symfony\Component\HttpFoundation\Response;

class UpdateTwitchUserFollowers implements ShouldQueue
{
    public function handle($event)
    {
        if($event instanceof Login) {
            $this->handleUserLogin($event);
            return;
        }

        $this->refreshFollowers($event);
    }

    private function refreshFollowers(UserConnectionChanged $event) {
        $twitchConnection = $event->user->connections()->where('service', 'twitch')->first();

        $userClient = new UsersClient();
        $followerList = $userClient
            ->withAppToken($twitchConnection->service_token)
            ->allFollowers($twitchConnection->service_user_id);

        $followerIds = [];
        foreach ($followerList['data'] as $followerData) {
            $followerIds[] = $followerData['from_id'];

            $followerModal = UserFollowers::withTrashed()
                ->where('twitch_user_id', $twitchConnection->service_user_id)
                ->where('follower_user_id', $followerData['from_id'])
                ->first();

            if($followerModal === null) {
                UserFollowers::create([
                    'twitch_user_id' => $twitchConnection->service_user_id,
                    'follower_user_id' => $followerData['from_id'],
                    'follower_username' => $followerData['from_name'],
                    'followed_at' => Carbon::parse($followerData['followed_at']),
                    'deleted_at' => null,
                ]);

                continue;
            }

            $followerModal->follower_username = $followerData['from_name'];
            $followerModal->followed_at = Carbon::parse($followerData['followed_at']);

            if ($followerModal->trashed()) {
                $followerModal->restore();
            }

            $followerModal->save();
        }

        UserFollowers::whereNotIn('follower_user_id', $followerIds)
            ->where('twitch_user_id', $twitchConnection->service_user_id)
            ->delete();
    }

    public function handleUserLogin(Login $loginEvent)
    {
        try {
            $this->handle(new UserConnectionChanged($loginEvent->user, 'twitch'));
        } catch (ClientException $clientException) {
            if (!$clientException->hasResponse()) {
                throw $clientException;
            }

            if ($clientException->getResponse()->getStatusCode() !== Response::HTTP_UNAUTHORIZED) {
                throw $clientException;
            }
        }
    }
}
