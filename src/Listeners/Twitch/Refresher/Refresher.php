<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch\Refresher;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Redbeed\OpenOverlay\Exceptions\WrongConnectionTypeException;
use Redbeed\OpenOverlay\Models\Twitch\UserFollowers;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\SubscriptionsClient;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;
use function head;

abstract class Refresher
{
    public static function saveFollowers(): bool
    {
        return config('openoverlay.service.twitch.save.follower', false) === true;
    }

    public static  function saveSubscriber(): bool
    {
        return config('openoverlay.service.twitch.save.subscriber', false) === true;
    }

    /**
     * @throws WrongConnectionTypeException
     */
    protected function refreshFollowers(Connection $twitchConnection)
    {
        if ($twitchConnection->service !== 'twitch') {
            throw new WrongConnectionTypeException('Twitch connection needed');
        }

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

            if ($followerModal === null) {
                UserFollowers::create([
                    'twitch_user_id'    => $twitchConnection->service_user_id,
                    'follower_user_id'  => $followerData['from_id'],
                    'follower_username' => $followerData['from_name'],
                    'followed_at'       => Carbon::parse($followerData['followed_at']),
                    'deleted_at'        => null,
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

    /**
     * @throws WrongConnectionTypeException
     */
    protected function refreshSubscriber(Connection $twitchConnection)
    {
        if ($twitchConnection->service !== 'twitch') {
            throw new WrongConnectionTypeException('Twitch connection needed');
        }

        $twitchUser = $this->twitchUser($twitchConnection->service_user_id);

        if (empty($twitchUser['broadcaster_type'])) {
            return;
        }

        $subscriptionsClient = new SubscriptionsClient();
        $subscriberList = $subscriptionsClient
            ->withAppToken($twitchConnection->service_token)
            ->all($twitchConnection->service_user_id);

        $subscriberIds = [];
        foreach ($subscriberList['data'] as $subscriberData) {
            $subscriberIds[] = $subscriberData['user_id'];

            $subscriberModal = UserSubscriber::withTrashed()
                ->where('twitch_user_id', $twitchConnection->service_user_id)
                ->where('subscriber_user_id', $subscriberData['user_id'])
                ->first();

            if ($subscriberModal === null) {
                UserSubscriber::create([
                    'twitch_user_id' => $twitchConnection->service_user_id,

                    'subscriber_user_id'    => $subscriberData['user_id'],
                    'subscriber_username'   => $subscriberData['user_name'],
                    'subscriber_login_name' => $subscriberData['user_login'],

                    'tier'      => $subscriberData['tier'],
                    'tier_name' => $subscriberData['plan_name'],

                    'is_gift'           => $subscriberData['is_gift'],
                    'gifter_user_id'    => $subscriberData['gifter_id'],
                    'gifter_username'   => $subscriberData['gifter_name'],
                    'gifter_login_name' => $subscriberData['gifter_login'],
                ]);

                continue;
            }

            $subscriberModal->subscriber_username = $subscriberData['user_name'];
            $subscriberModal->subscriber_login_name = $subscriberData['user_login'];

            $subscriberModal->tier = $subscriberData['tier'];
            $subscriberModal->tier_name = $subscriberData['plan_name'];

            $subscriberModal->is_gift = $subscriberData['is_gift'];
            $subscriberModal->gifter_user_id = $subscriberData['gifter_id'];
            $subscriberModal->gifter_username = $subscriberData['gifter_name'];
            $subscriberModal->gifter_login_name = $subscriberData['gifter_login'];

            if ($subscriberModal->trashed()) {
                $subscriberModal->restore();
            }

            $subscriberModal->save();
        }

        UserSubscriber::whereNotIn('subscriber_user_id', $subscriberIds)
            ->where('twitch_user_id', $twitchConnection->service_user_id)
            ->delete();
    }

    private function twitchUser(string $broadcasterId): array
    {
        $userClient = new UsersClient();
        return head(Arr::get($userClient->byId($broadcasterId), 'data', []));
    }

}
