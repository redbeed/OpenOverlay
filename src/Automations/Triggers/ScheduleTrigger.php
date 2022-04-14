<?php

namespace Redbeed\OpenOverlay\Automations\Triggers;

use Carbon\Carbon;

class ScheduleTrigger extends Trigger
{
    public static string $name = 'Schedule';
    public static string $description = 'Trigger automation when based on time';

    public Carbon $date;

    public function __construct()
    {
        $this->date = now();
    }
}
