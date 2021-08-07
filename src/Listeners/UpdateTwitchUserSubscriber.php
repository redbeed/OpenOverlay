<?php

namespace Redbeed\OpenOverlay\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;
use Redbeed\OpenOverlay\Service\Twitch\SubscriptionsClient;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;

class UpdateTwitchUserSubscriber implements ShouldQueue
{
    public function handle(UserConnectionChanged $event)
    {
        $twitchConnection = $event->user->connections()->where('service', 'twitch')->first();
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

                    'subscriber_user_id' => $subscriberData['user_id'],
                    'subscriber_username' => $subscriberData['user_name'],
                    'subscriber_login_name' => $subscriberData['user_login'],

                    'tier' => $subscriberData['tier'],
                    'tier_name' => $subscriberData['plan_name'],

                    'is_gift' => $subscriberData['is_gift'],
                    'gifter_user_id' => $subscriberData['gifter_id'],
                    'gifter_username' => $subscriberData['gifter_name'],
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
