<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Redbeed\OpenOverlay\Service\Twitch\EventSubClient;

class EventSubListingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:event-sub:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all active EventSub subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $eventSubClient = new EventSubClient();
        $subData = $eventSubClient->listSubscriptions();

        $this->subscriptionsTable($subData['data']);
        $this->info('Total EventSub subscriptions: '.$subData['total']);
    }

    protected function subscriptionsTable(array $subscriptions): void
    {
        $subscriptions = array_map(function ($data) {
            $information = Arr::only($data, ['id', 'status', 'type', 'condition', 'created_at']);
            $information['condition'] = json_encode($information['condition']);

            return $information;
        }, $subscriptions);

        $this->table(
            ['Id', 'Status', 'Type', 'Condition', 'Created at'],
            $subscriptions,
            'box'
        );
    }
}
