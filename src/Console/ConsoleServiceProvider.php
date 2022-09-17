<?php

namespace Redbeed\OpenOverlay\Console;

use Illuminate\Support\ServiceProvider;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\RestartServerCommand;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\SendMessageCommand;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\StartCommand;
use Redbeed\OpenOverlay\Console\Commands\EventBroadcastFaker;
use Redbeed\OpenOverlay\Console\Commands\EventSubDeleteCommand;
use Redbeed\OpenOverlay\Console\Commands\EventSubListingCommand;
use Redbeed\OpenOverlay\Console\Commands\SecretCommand;
use Redbeed\OpenOverlay\Console\Commands\Twitch\OnlineStatusCommand;
use Redbeed\OpenOverlay\Console\Commands\Twitch\RefresherCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerGlobalCommands();

        if ($this->app->runningInConsole()) {
            $this->registerConsoleCommands();
        }
    }

    protected function registerConsoleCommands(): void
    {
        $this->commands([
            EventSubListingCommand::class,
            EventSubDeleteCommand::class,
            EventBroadcastFaker::class,
            SecretCommand::class,
            StartCommand::class,
            RestartServerCommand::class,

            RefresherCommand::class,
            OnlineStatusCommand::class,
        ]);
    }

    protected function registerGlobalCommands(): void
    {
        $this->commands([
            SendMessageCommand::class,
        ]);
    }
}
