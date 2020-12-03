<?php

namespace Redbeed\OpenOverlay\Sociallite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class TwitchClientCredentialsExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('twitch_client_credentials', TwitchClientCredentialsProvider::class);
    }
}
