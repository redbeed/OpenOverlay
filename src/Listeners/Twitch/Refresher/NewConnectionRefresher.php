<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch\Refresher;

use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\UserConnectionChanged;
use Redbeed\OpenOverlay\Exceptions\WrongConnectionTypeException;

class NewConnectionRefresher extends Refresher implements ShouldQueue
{
    /**
     * @throws WrongConnectionTypeException
     */
    public function handle(UserConnectionChanged $event)
    {
        if ($event->service !== 'twitch') {
            return;
        }

        $twitchConnection = $event->user
            ->connections()
            ->where('service', 'twitch')
            ->first();

        if (parent::saveFollowers()) {
            $this->refreshFollowers($twitchConnection);
        }

        if (parent::saveSubscriber()) {
            $this->refreshSubscriber($twitchConnection);
        }
    }
}
