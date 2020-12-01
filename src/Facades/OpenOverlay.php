<?php

namespace Redbeed\OpenOverlay\Facades;

use Illuminate\Support\Facades\Facade;

class OpenOverlay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'openoverlay';
    }
}
