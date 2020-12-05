<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use Carbon\Carbon;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelSubscribeFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelUpdateFake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\Fake;
use Redbeed\OpenOverlay\Console\Commands\BroadcastFaker\ChannelFollowFake;
use Redbeed\OpenOverlay\Events\TwitchEventReceived;
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
        $fakeEvent = new EventSubEvents();
        $fakeEvent->event_type = $type;
        $fakeEvent->event_user_id = $twitchUserId;
        $fakeEvent->event_data = $fakeModel::value();
        $fakeEvent->event_sent = Carbon::now();

        broadcast(new TwitchEventReceived($fakeEvent));
        $this->info('Event '.$type.' for '.$twitchUserId.' fired');
    }
}
