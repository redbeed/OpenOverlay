<?php

namespace Redbeed\OpenOverlay;

use \Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Redbeed\OpenOverlay\Events\Twitch\BotTokenExpires;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Listeners\Twitch\NewFollowerListener;
use Redbeed\OpenOverlay\Listeners\Twitch\NewSubscriberListener;
use Redbeed\OpenOverlay\Listeners\AutoShoutOutRaid;
use Redbeed\OpenOverlay\Listeners\TwitchSplitReceivedEvents;
use Redbeed\OpenOverlay\Listeners\Twitch\UpdateBotToken;
use Redbeed\OpenOverlay\Listeners\Twitch\Refresher\NewConnectionRefresher;
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
        BotTokenExpires::class    => [
            UpdateBotToken::class
        ]
    ];

    public function listens(): array
    {
        $this->autoShoutOutListener();
        $listen = $this->listen;

        $listen[UserConnectionChanged::class] = [
            UpdateUserWebhookCalls::class,
        ];

        $listen[EventReceived::class][] = TwitchSplitReceivedEvents::class;
        $listen[UserConnectionChanged::class][] = NewConnectionRefresher::class;

        if (config('openoverlay.service.twitch.save.follower', false) === true) {
            $listen[EventReceived::class][] = NewFollowerListener::class;
        }

        if (config('openoverlay.service.twitch.save.subscriber', false) === true) {
            $listen[EventReceived::class][] = NewSubscriberListener::class;
        }

        return $listen;
    }

    public function autoShoutOutListener()
    {
        $modules = config('openoverlay.modules', []);
        if (empty($modules[AutoShoutOutRaid::class]) || empty($modules[AutoShoutOutRaid::class]['message'])) {
            return;
        }

        $this->listen[EventReceived::class][] = AutoShoutOutRaid::class;
    }
}
