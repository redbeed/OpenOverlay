<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Redbeed\OpenOverlay\Models\Twitch\EventSubscription;
use Redbeed\OpenOverlay\Service\Twitch\EventSubClient;

class EventSubDeleteCommand extends EventSubListingCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:event-sub:delete
        {--all : Delete all subscription}
        {--status= : Delete by status (like: "webhook_callback_verification_failed")}
        {--type= : Delete by type (like: "channel.update")}
        {--id= : Delete by subscription-id}
        {--condition= : Delete by string matching condition (JSON)}
        {--list : List subscription before deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete EventSub subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function validateOptions(): bool
    {
        $all = $this->option('all');
        $type = $this->option('type');
        $status = $this->option('status');
        $subId = $this->option('id');
        $condition = $this->option('condition');

        if ($all === true) {
            return true;
        }

        if (empty($type) === false || empty($subId) === false || empty($condition) === false || empty($status) === false) {
            return true;
        }

        return false;
    }

    public function handle(): void
    {
        if ($this->validateOptions() === false) {
            $this->error('Options not valid');

            return;
        }

        $eventSubClient = EventSubClient::http();
        $subscriptions = $eventSubClient->subscriptions();

        if ($this->option('all') === false) {
            $subscriptions = $this->findByStatus($subscriptions);
            $subscriptions = $this->findByType($subscriptions);
            $subscriptions = $this->findById($subscriptions);
            $subscriptions = $this->findByCondition($subscriptions);
        }

        $subscriptionsCount = $subscriptions->count();

        if ($subscriptionsCount === 0) {
            $this->error('No matching subscription founded');

            return;
        }

        if ($this->option('list')) {
            $this->subscriptionsTable($subscriptions);
        }

        $this->info($subscriptionsCount.' subscriptions matching your options');

        if ($this->confirm('Do you wish to delete them?')) {
            $deleteProgress = $this->output->createProgressBar($subscriptionsCount);
            $deleteProgress->start();

            $deleted = 0;

            foreach ($subscriptions as $subscription) {
                try {
                    $eventSubClient->deleteSubscription($subscription['id']);
                    $deleted++;
                } catch (RequestException $exception) {
                    $this->error($subscription['id'].' could not deleted');
                }

                $deleteProgress->advance();
            }

            $deleteProgress->finish();
            $this->newLine(2);

            $this->info('Total EventSub deleted: '.$deleted.'/'.$subscriptionsCount);
        }
    }

    private function findByCondition(Collection $subscriptions): Collection
    {
        $condition = $this->option('condition');

        if (empty($condition)) {
            return $subscriptions;
        }

        // clean up
        $condition = json_encode(json_decode($condition, true));

        return $subscriptions->filter(function ($subscription) use ($condition) {
            /** @var EventSubscription $subscription */
            return $subscription->condition === $condition;
        });
    }

    private function findById(Collection $subscriptions): Collection
    {
        $subId = $this->option('id');

        if (empty($subId)) {
            return $subscriptions;
        }

        return $subscriptions->filter(function ($subscription) use ($subId) {
            /** @var EventSubscription $subscription */
            return $subscription->id === $subId;
        });
    }

    private function findByType(Collection $subscriptions): Collection
    {
        $type = $this->option('type');

        if (empty($type)) {
            return $subscriptions;
        }

        return $subscriptions->filter(function ($subscription) use ($type) {
            /** @var EventSubscription $subscription */
            return $subscription->type === $type;
        });
    }

    private function findByStatus(Collection $subscriptions): Collection
    {
        $status = $this->option('status');

        if (empty($status)) {
            return $subscriptions;
        }

        return $subscriptions->filter(function ($subscription) use ($status) {
            /** @var EventSubscription $subscription */
            return $subscription->status === $status;
        });
    }
}
