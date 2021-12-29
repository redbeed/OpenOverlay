<?php

namespace Redbeed\OpenOverlay\Console\Commands\Twitch;

use Illuminate\Console\Command;
use Redbeed\OpenOverlay\Events\Twitch\RefresherEvent;
use Redbeed\OpenOverlay\Models\User\Connection;

class RefresherCommand extends Command
{

    protected $signature = 'overlay:twitch:refresher';

    protected $description = 'Refresh followers and subscriber for all twitch connections';

    public function handle(): void
    {
        $connections = Connection::where('service', 'twitch')->get();

        foreach ($connections as $connection) {
            $this->info('Start for ' . $connection->service_username . ' (' . $connection->service_user_id . ')');

            event(new RefresherEvent($connection));
        }
    }
}
