# OpenOverlay

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

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

Add ENV Keys

``` bash
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=
TWITCH_REDIRECT_URI=${APP_URL}/connection/callback

OVERLAY_SECRET=
OVERLAY_TWITCH_APP_TOKEN=
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

2. Open `${APP_URL}/connection/api-token/redirect` with your laravel-app.
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
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/redbeed
[link-author-chris]: https://github.com/redbeed
[link-contributors]: ../../contributors
