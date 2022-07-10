<?php

namespace Redbeed\OpenOverlay\Console\Commands\BroadcastFaker;

class ChannelCheerFake extends Fake
{
    protected $eventData = [
        'is_anonymous' => false,
        'user_id' => '1234',
        'user_login' => null,
        'user_name' => null,
        'broadcaster_user_id' => '1337',
        'broadcaster_user_login' => 'cooler_user',
        'broadcaster_user_name' => 'Cooler_User',
        'message' => 'This is a bit cheer for you!',
        'bits' => 1000,
    ];

    protected function randomizeEventData(): array
    {
        $array = parent::randomizeEventData();

        $anonymous = (bool) random_int(0, 1);

        if ($anonymous === false) {
            $username = Fake::fakeUsername();
            $array['user_name'] = $username;
            $array['user_login'] = strtolower($username);
        }

        $array['bits'] = random_int(10, 5000);

        return $array;
    }
}
