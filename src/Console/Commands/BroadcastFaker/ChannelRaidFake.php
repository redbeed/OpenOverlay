<?php

namespace Redbeed\OpenOverlay\Console\Commands\BroadcastFaker;

class ChannelRaidFake extends Fake
{

    protected $eventData = [
        "from_broadcaster_user_id" => "1234",
        "from_broadcaster_user_login" => "cool_user",
        "from_broadcaster_user_name" => "Cool_User",
        "to_broadcaster_user_id" => "1337",
        "to_broadcaster_user_login" => "cooler_user",
        "to_broadcaster_user_name" => "Cooler_User",
        "viewers" => 9001
    ];

    protected function randomizeEventData(): array
    {
        $array = parent::randomizeEventData();

        $username = Fake::fakeUsername();
        $array['from_broadcaster_user_login'] = $username;
        $array['from_broadcaster_user_name'] = strtolower($username);

        $array['viewers'] = random_int(1, 9999);

        return $array;
    }
}
