<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use Carbon\Carbon;

class DateTime
{
    public static function parse($dateString): Carbon
    {
        // twitch timestamps sometimes (randomly) to long
        $timestamp = substr(trim($dateString, 'Z'), 0, 23) . 'Z';

        return Carbon::createFromFormat(\DateTime::RFC3339_EXTENDED, $timestamp);
    }
}
