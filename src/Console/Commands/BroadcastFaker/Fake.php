<?php

namespace Redbeed\OpenOverlay\Console\Commands\BroadcastFaker;

use Illuminate\Support\Arr;

class Fake
{
    protected $eventData = [];

    public static function value(): array
    {
        return (new static())->fakeValue();
    }

    protected function randomizeEventData(): array
    {
        return $this->eventData;
    }

    public function fakeValue(): array
    {
        return $this->randomizeEventData();
    }

    public static function fakeUsername(): string
    {
        return Arr::random([
            'Chris',
            'redbeed',
            'moVRs',
            'Lethinium',
            'kekub',
            'Laravel_user',
            'Twitch_user',
        ]);
    }
}
