<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use Redbeed\OpenOverlay\Service\Twitch\EventSubClient;

class SecretCommand extends EventSubListingCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new secret for safer twitch communication';


    public function handle(): void
    {
        $currentSecret = env('OVERLAY_SECRET');
        if (empty($currentSecret) === false) {
            $this->warn('You already have a secret');
            $this->newLine(2);
        }

        $newSecret = Str::random(20);

        $this->info('You need secret: '.$newSecret);

        $this->newLine();

        $this->line('Please add this into your .env file');
        $this->line('OVERLAY_SECRET="'.$newSecret.'"');
    }
}
