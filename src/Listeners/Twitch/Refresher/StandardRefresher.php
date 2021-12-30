<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch\Refresher;

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
            $this->refreshSubscriber($event->twitchConnection);
        }
    }
}
