<?php

namespace Redbeed\OpenOverlay\Tests;

use SocialiteProviders\Manager\ServiceProvider as SocialiteServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Redbeed\OpenOverlay\OpenOverlayServiceProvider;
use Redbeed\OpenOverlay\Sociallite\TwitchClientCredentialsProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OpenOverlayServiceProvider::class,
            SocialiteServiceProvider::class,
            TwitchClientCredentialsProvider::class,
        ];
    }
}
