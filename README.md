# OpenOverlay

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

Receive events from Twitch-EventSub Api with Laravel.

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
'twitch_client_credentials' => [    
  'client_id' => env('TWITCH_CLIENT_ID'),  
  'client_secret' => env('TWITCH_CLIENT_SECRET'),  
  'redirect' => env('TWITCH_APP_TOKEN_REDIRECT_URI') 
],
```
_Thanks to [SocialiteProviders/Twitch][link-socialite]_

Add ENV Keys

``` bash
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=
TWITCH_REDIRECT_URI=${APP_URL}/connection/callback
TWITCH_APP_TOKEN_REDIRECT_URI=${APP_URL}/connection/app-token/callback

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

## Generate Secret
To validate each Twitch call you need to generate a secret for your app.
If you change the `OVERLAY_SECRET` you need to subscribe each event again.

``` bash
php artisan overlay:secret
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
[ico-travis]: https://img.shields.io/travis/redbeed/openoverlay/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/redbeed/openoverlay
[link-downloads]: https://packagist.org/packages/redbeed/openoverlay
[link-travis]: https://travis-ci.org/redbeed/openoverlay
[link-socialite]: https://github.com/SocialiteProviders/Twitch
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/redbeed
[link-author-chris]: https://github.com/redbeed
[link-contributors]: ../../contributors
