<?php

namespace Redbeed\OpenOverlay\Console\Commands\BroadcastFaker;

class ChannelSubscribeFake extends Fake
{
    protected $eventData = [
        "user_id" => "1234",
        "user_name" => "cool_user",
        "broadcaster_user_id" => "1337",
        "broadcaster_user_name" => "cooler_user",
        "tier" => "1000",
        "is_gift" => false,
    ];

    protected function randomizeEventData(): array
    {
        $array = parent::randomizeEventData();
        $array['user_name'] = Fake::fakeUsername();

        return $array;
    }
}
