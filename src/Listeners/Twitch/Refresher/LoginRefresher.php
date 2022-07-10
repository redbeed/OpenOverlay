<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch\Refresher;

use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Exceptions\WrongConnectionTypeException;

class LoginRefresher extends Refresher implements ShouldQueue
{
    /**
     * @throws WrongConnectionTypeException
     */
    public function handle(Login $event)
    {
        $twitchConnection = $event->user
            ->connections()
            ->where('service', 'twitch')
            ->first();

        if (empty($twitchConnection)) {
            return;
        }

        if (parent::saveFollowers()) {
            $this->refreshFollowers($twitchConnection);
        }

        if (parent::saveSubscriber()) {
            try {
                $this->refreshSubscriber($twitchConnection);
            } catch (Exception $e) {
                // ignore exception as it is not critical
                // user auth token is not valid
                report($e);
            }
        }
    }
}
