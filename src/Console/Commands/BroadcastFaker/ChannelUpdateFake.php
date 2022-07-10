<?php

namespace Redbeed\OpenOverlay\Console\Commands\BroadcastFaker;

use Carbon\Carbon;

class ChannelUpdateFake extends Fake
{
    protected $eventData = [
        'user_id' => '1337',
        'user_name' => 'open_overlay_user',
        'title' => 'Best Stream Ever',
        'language' => 'en',
        'category_id' => '21779',
        'category_name' => 'Fortnite',
        'is_mature' => false,
    ];

    protected function randomizeEventData(): array
    {
        $array = parent::randomizeEventData();

        $array['title'] = implode(' ', [
            $array['title'],
            '('.Carbon::now()->format('H:i:s').')',
        ]);
        $array['user_name'] = Fake::fakeUsername();

        return $array;
    }
}
