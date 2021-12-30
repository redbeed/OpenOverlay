<?php

namespace Redbeed\OpenOverlay\Console\Commands\ChatBot;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\InteractsWithTime;

class RestartServerCommand extends Command
{
    use InteractsWithTime;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:chatbot:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart Chat Bot';

    public function handle(): void
    {

        Cache::forever(
            StartCommand::RESTART_CACHE_KEY,
            $this->currentTime()
        );

        $this->info('Broadcasted restart signal to Chat Bot Service!');
    }


}
