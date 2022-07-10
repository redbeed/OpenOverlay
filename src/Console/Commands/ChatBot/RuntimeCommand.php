<?php

namespace Redbeed\OpenOverlay\Console\Commands\ChatBot;

use Illuminate\Console\Command;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

abstract class RuntimeCommand extends Command
{
    /**
     * Get Loop instance
     */
    protected LoopInterface $loop;

    public function __construct()
    {
        parent::__construct();

        $this->loop = Loop::get();
    }

    protected function softShutdown()
    {
        $this->loop->stop();

        echo 'Chatbot Service will shutdown.'.PHP_EOL;
    }
}
