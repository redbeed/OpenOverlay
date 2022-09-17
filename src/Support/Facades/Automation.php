<?php

namespace Redbeed\OpenOverlay\Support\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void add(string $trigger, string|array|Closure $handler)
 */
class Automation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'automations';
    }
}
