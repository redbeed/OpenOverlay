<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Redbeed\OpenOverlay\Models\Twitch\EventSubscription;
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
     */
    public function handle()
    {
        $eventSubClient = EventSubClient::http();
        $subscriptions = $eventSubClient->subscriptions();

        $this->subscriptionsTable($subscriptions);
        $this->info('Total EventSub subscriptions: '.$subscriptions->count());
    }

    protected function subscriptionsTable(Collection $subscriptions): void
    {
        $subscriptions = $subscriptions->map(function ($subscription) {
            /** @var EventSubscription $subscription */

            return [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'type' => $subscription->type,
                'condition' => json_encode($subscription->condition),
                'created_at' => $subscription->createdAt->toDateTimeLocalString(),
            ];
        });

        $this->table(
            ['Id', 'Status', 'Type', 'Condition', 'Created at'],
            $subscriptions,
            'box'
        );
    }
}
