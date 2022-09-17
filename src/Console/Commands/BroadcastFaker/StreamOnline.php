<?php

namespace Redbeed\OpenOverlay\Console\Commands\BroadcastFaker;

use Carbon\Carbon;

class StreamOnline extends Fake
{
    protected $eventData = [
        'id' => '1337',
        'broadcaster_user_id' => 'u1337',
        'broadcaster_user_login' => 'cooler_user',
        'broadcaster_user_name' => 'Cooler_user',
        'type' => 'live',
        'started_at' => null,
    ];

    protected function randomizeEventData(): array
    {
        $array = parent::randomizeEventData();

        $array['started_at'] = Carbon::now()->toIso8601String();

        return $array;
    }
}
