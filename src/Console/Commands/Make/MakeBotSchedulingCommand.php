<?php

namespace Redbeed\OpenOverlay\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeBotSchedulingCommand extends GeneratorCommand
{
    protected $name = 'make:bot-schedule';

    protected $description = 'Create OpenOverlay Bot schedule message';

    protected $type = 'OpenOverlay bot schedule message';

    protected function getStub(): string
    {
        $relativePath = '/Stubs/BotScheduling.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : __DIR__.$relativePath;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Bot\Scheduling';
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the bot scheduling message'],
        ];
    }
}
