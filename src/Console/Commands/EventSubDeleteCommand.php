<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use GuzzleHttp\Exception\RequestException;
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
        $subData = $eventSubClient->listSubscriptions();
        $subscriptions = $subData['data'];

        if ($this->option('all') === false) {
            $subscriptions = $this->findByStatus($subscriptions);
            $subscriptions = $this->findByType($subscriptions);
            $subscriptions = $this->findById($subscriptions);
            $subscriptions = $this->findByCondition($subscriptions);
        }

        $subscriptionsCount = count($subscriptions);

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

    private function findByCondition(array $subscriptions): array
    {
        $condition = $this->option('condition');

        if (empty($condition)) {
            return $subscriptions;
        }

        // clean up
        $condition = json_encode(json_decode($condition, true));

        return array_filter($subscriptions, static function ($subscription) use ($condition) {
            return json_encode($subscription['condition']) === $condition;
        });
    }

    private function findById(array $subscriptions): array
    {
        $subId = $this->option('id');

        if (empty($subId)) {
            return $subscriptions;
        }

        return array_filter($subscriptions, static function ($subscription) use ($subId) {
            return $subscription['id'] === $subId;
        });
    }

    private function findByType(array $subscriptions): array
    {
        $type = $this->option('type');

        if (empty($type)) {
            return $subscriptions;
        }

        return array_filter($subscriptions, static function ($subscription) use ($type) {
            return $subscription['type'] === $type;
        });
    }

    private function findByStatus(array $subscriptions): array
    {
        $status = $this->option('status');

        if (empty($status)) {
            return $subscriptions;
        }

        return array_filter($subscriptions, static function ($subscription) use ($status) {
            return $subscription['status'] === $status;
        });
    }
}
