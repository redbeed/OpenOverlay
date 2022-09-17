<?php

namespace Redbeed\OpenOverlay\Console\Commands\Twitch;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\StreamsClient;
use Redbeed\OpenOverlay\Support\StreamerOnline;

class OnlineStatusCommand extends Command
{
    protected $signature = 'overlay:twitch:online-status {twitchUserId?} {--all=false}';

    protected $description = 'Checks online status of twitch user';

    public function handle(): void
    {
        $connections = Connection::where('service', 'twitch');

        if (! $this->option('all') && $this->argument('twitchUserId')) {
            $connections = $connections->where('service_user_id', $this->argument('twitchUserId'));
        }

        $connections = $connections->get();
        $streamsResponse = (new StreamsClient())->byUserIds($connections->pluck('service_user_id')->toArray());
        $streamsData = collect($streamsResponse['data'] ?? []);

        foreach ($connections as $connection) {
            $stream = $streamsData->firstWhere('user_id', $connection->service_user_id);

            if ($stream !== null) {
                StreamerOnline::setOnline(
                    $connection->service_user_id,
                    Carbon::parse($stream['created_at'] ?? null, 'UTC')
                );

                $this->info('Streamer '.$connection->service_username.' is online');
                continue;
            }

            $this->info('Streamer '.$connection->service_username.' is offline');
            StreamerOnline::setOffline($connection->service_user_id);
        }
    }
}
