<?php

namespace Redbeed\OpenOverlay;

use \Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Listeners\UpdateUserWebhookCalls;
use Redbeed\OpenOverlay\Sociallite\TwitchClientCredentialsExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Twitch\TwitchExtendSocialite;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        UserConnectionChanged::class => [
            UpdateUserWebhookCalls::class,
        ],

        SocialiteWasCalled::class => [
            TwitchExtendSocialite::class,
            TwitchClientCredentialsExtendSocialite::class,
        ],
    ];

}
