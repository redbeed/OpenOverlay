<?php

namespace Redbeed\OpenOverlay;

use \Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Redbeed\OpenOverlay\Events\TwitchEventReceived;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Listeners\AddTwitchUserFollower;
use Redbeed\OpenOverlay\Listeners\AddTwitchUserSubscriber;
use Redbeed\OpenOverlay\Listeners\UpdateTwitchUserFollowers;
use Redbeed\OpenOverlay\Listeners\UpdateTwitchUserSubscriber;
use Redbeed\OpenOverlay\Listeners\UpdateUserWebhookCalls;
use Redbeed\OpenOverlay\Sociallite\TwitchClientCredentialsExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Twitch\TwitchExtendSocialite;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        SocialiteWasCalled::class => [
            TwitchExtendSocialite::class,
            TwitchClientCredentialsExtendSocialite::class,
        ],
    ];

    public function listens(): array
    {
        $listen = $this->listen;
        $listen[UserConnectionChanged::class] = [
            UpdateUserWebhookCalls::class,
        ];

        if (config('openoverlay.service.twitch.save.follower', false) === true) {
            $listen[UserConnectionChanged::class][] = UpdateTwitchUserFollowers::class;
            $listen[TwitchEventReceived::class][] = AddTwitchUserFollower::class;
        }

        if (config('openoverlay.service.twitch.save.subscriber', false) === true) {
            $listen[UserConnectionChanged::class][] = UpdateTwitchUserSubscriber::class;
            $listen[TwitchEventReceived::class][] = AddTwitchUserSubscriber::class;
        }

        return $listen;
    }
}
