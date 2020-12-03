<?php

namespace Redbeed\OpenOverlay\Sociallite;

use SocialiteProviders\Twitch\Provider as TwitchProvider;

class TwitchClientCredentialsProvider extends TwitchProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'TWITCH_CLIENT_CREDENTIALS';

    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'client_credentials',
        ]);
    }
}
