<?php

return [
    'route_prefix' => null,

    'service' => [
        'twitch' => [
            'scopes' => [ // https://dev.twitch.tv/docs/authentication/#scopes
                'user:read:email', 'user:read:broadcast',
                'channel:read:subscriptions', 'channel:read:subscriptions',
                'bits:read',
            ],
        ],
    ],

    'webhook' => [
        'twitch' => [
            'app_token' => [
                'token' => env('OVERLAY_TWITCH_APP_TOKEN'),
                'regenerate' => false,
            ],

            /** A secret is needed to check webhook calls by twitch */
            'secret' => env('OVERLAY_SECRET'),

            'subscribe' => [ // https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelupdate
                'stream.online', 'stream.offline',
                'channel.update', 'channel.follow',
                'channel.subscribe', 'channel.cheer',
            ],
        ],
    ],
];
