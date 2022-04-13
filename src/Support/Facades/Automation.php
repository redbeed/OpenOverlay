<?php

namespace Redbeed\OpenOverlay\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Redbeed\OpenOverlay\Automations\AutomationHandler;

/**
 * @method static void add(string $trigger, string|array $handler)
 */
class Automation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'automations';
    }
}
