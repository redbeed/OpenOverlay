<?php

namespace Redbeed\OpenOverlay\Automations\Triggers;

use Carbon\Carbon;

class ScheduleTrigger extends Trigger
{
    public Carbon $date;

    public function __construct()
    {
        $this->date = now();
    }
}
