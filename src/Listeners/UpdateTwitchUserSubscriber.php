<?php

namespace Redbeed\OpenOverlay\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Models\Twitch\UserSubscriber;
use Redbeed\OpenOverlay\Service\Twitch\SubscriptionsClient;

class UpdateTwitchUserSubscriber implements ShouldQueue
{
    public function handle(UserConnectionChanged $event)
    {
        $twitchConnection = $event->user->connections()->where('service', 'twitch')->first();

        $subscriberList = SubscriptionsClient::withAppToken($twitchConnection->service_token)
            ->all($twitchConnection->service_user_id);

        $subscriberIds = [];
        foreach ($subscriberList['data'] as $subscriberData) {
            $subscriberIds[] = $subscriberData['user_id'];

            UserSubscriber::firstOrCreate([
                'twitch_user_id' => $twitchConnection->service_user_id,
                'subscriber_user_id' => $subscriberData['user_id'],
            ], [
                'subscriber_username' => $subscriberData['user_name'],
                'tier' => $subscriberData['user_name'],
                'tier_name' => $subscriberData['plan_name'],
                'is_gift' => $subscriberData['is_gift']
            ]);
        }

        UserSubscriber::whereNotIn('subscriber_user_id', $subscriberIds)
            ->where('twitch_user_id', $twitchConnection->service_user_id)
            ->delete();
    }

}
