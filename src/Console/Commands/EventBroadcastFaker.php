<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelCheerFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelRaidFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelSubscribeFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelUpdateFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\Fake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelFollowFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\StreamOffline;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\StreamOnline;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;

class EventBroadcastFaker extends EventSubListingCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:broadcast:faker {twitchUserId} {type : Event type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete EventSub subscriptions';

    protected $types = [
        'channel.update' => ChannelUpdateFake::class,
        'channel.follow' => ChannelFollowFake::class,
        'channel.subscribe' => ChannelSubscribeFake::class,
        'channel.cheer' => ChannelCheerFake::class,
        'channel.raid' => ChannelRaidFake::class,
        'stream.online' => StreamOnline::class,
        'stream.offline' => StreamOffline::class,
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $twitchUserId = $this->argument('twitchUserId');
        $type = $this->argument('type');

        if (empty($twitchUserId)) {
            $this->error('Twitch user id not given');

            return;
        }

        if (!array_key_exists($type, $this->types)) {
            $this->error('Type is not provided');

            return;
        }

        /** @var Fake $fakeModel */
        $fakeModel = $this->types[$type];
        $fakeEvent = EventSubEvents::factory()->create([
            'event_user_id' => $twitchUserId,
            'event_type' => $type,
            'event_data' => $fakeModel::value(),
        ]);

        broadcast(new EventReceived($fakeEvent));
        $this->info('Event ' . $type . ' for ' . $twitchUserId . ' fired');
    }
}
