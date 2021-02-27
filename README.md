# OpenOverlay

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

OpenOverlay is a self-hosted service for your web-based twitch overlays and bot.
This Laravel package helps you to receive all twitch events actions while you streaming and show them on your overlay.
Also, you can develop your own bot with simple and advanced commands.

If you want to start from scratch with your overlay we have an example project.
Standalone-Version: _[redbeed/OpenOverlay-Standalone][link-standalone]_

## Installation

Via Composer

``` bash
$ composer require redbeed/openoverlay
```

### Configuring

Add laravel config for OpenOverlay

``` bash
php artisan vendor:publish --provider="Redbeed\OpenOverlay\OpenOverlayServiceProvider"
```

Migrate User Connections & Twitch Event table

``` bash
php artisan migrate
```

Add configuration to `config/services.php`

```php
'twitch' => [    
  'client_id' => env('TWITCH_CLIENT_ID'),  
  'client_secret' => env('TWITCH_CLIENT_SECRET'),  
  'redirect' => env('TWITCH_REDIRECT_URI') 
],
```
_Thanks to [SocialiteProviders/Twitch][link-socialite]_

Add ENV Keys

``` bash
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=

OVERLAY_SECRET=
OVERLAY_TWITCH_APP_TOKEN=
```

Add `UserOpenOverlay` trait to `User.php`

``` php
<?php

namespace App\Models;

...
use \Redbeed\OpenOverlay\Models\User\UserOpenOverlay;

class User extends Authenticatable
{
    use ...
    use UserOpenOverlay;
```

Add Callback URLs to your Twitch App

``` bash
${APP_URL}/connection/callback
${APP_URL}/connection/app-token/callback
${APP_URL}/connection/bot/callback
```

### Generate APP Token
To subscribe the Twitch-EventSub you need to generate an App-Token.

1. First you need to enable the "app token routes" in the `openoverlay.php` config.
    ``` php
    return [
        ...
        'webhook' => [
            'twitch' => [
                'app_token' => [
                    'regenerate' => true,
                ],
            ]
        ]
        ...
    ```

   Set `regenerate` to `true`.

2. Open `${APP_URL}/connection/app-token/redirect` with your laravel-app.
3. Login into your Twitch-Developer account with your Twitch Application.
4. Copy the App-Token and use it as value for your  `OVERLAY_TWITCH_APP_TOKEN` ENV value.

### Add Bot 
To add a bot you need to link your app with the bot twitch account.

1. Open `${APP_URL}/connection/bot/redirect` with your laravel-app.
2. Login into your Twitch-Bot account with your Twitch Application.
3. After redirect you need to manually connect your laravel-Account with a bot.
4. Open Your Database table "bot_connections" and connect your bot with your user.
5. Restart the Bot Artisan Bot


## Generate Secret
To validate each Twitch call you need to generate a secret for your app.
If you change the `OVERLAY_SECRET` you need to subscribe each event again.

``` bash
php artisan overlay:secret
```

## Send Fake Events

You can send "Fake" Events while developing or testing an overlay.
``` bash
php artisan {TwitchUserId} {EventType}
php artisan 1337 channel.follow
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [redbeed][link-author]
- [Chris Woelk][link-author-chris]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/redbeed/openoverlay.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/redbeed/openoverlay.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/redbeed/openoverlay
[link-downloads]: https://packagist.org/packages/redbeed/openoverlay
[link-travis]: https://travis-ci.org/redbeed/openoverlay
[link-socialite]: https://github.com/SocialiteProviders/Twitch
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/redbeed
[link-author-chris]: https://github.com/chris-redbeed
[link-contributors]: ../../contributors
[link-standalone]: https://github.com/redbeed/OpenOverlay-Standalone
