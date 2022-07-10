<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch\Refresher;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\Twitch\RefresherEvent;
use Redbeed\OpenOverlay\Exceptions\WrongConnectionTypeException;

class StandardRefresher extends Refresher implements ShouldQueue
{
    /**
     * @throws WrongConnectionTypeException
     */
    public function handle(RefresherEvent $event)
    {
        if ($event->twitchConnection->service !== 'twitch') {
            return;
        }

        if (parent::saveFollowers()) {
            $this->refreshFollowers($event->twitchConnection);
        }

        if (parent::saveSubscriber()) {
            try {
                $this->refreshSubscriber($event->twitchConnection);
            } catch (ClientException $e) {
                // ignore exception as it is not critical
                // user auth token is not valid
                report($e);
            }
        }
    }
}
