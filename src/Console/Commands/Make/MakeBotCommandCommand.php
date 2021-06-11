<?php

namespace Redbeed\OpenOverlay\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeBotCommandCommand extends GeneratorCommand
{
    protected $name = 'make:bot-command';

    protected $description = 'Create OpenOverlay Bot Command';

    protected $type = 'OpenOverlay bot command';

    protected function getStub(): string
    {
        $relativePath = '/Stubs/BotCommand.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : __DIR__.$relativePath;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Bot\Commands';
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the bot command (example: !hello)'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['command', null, InputOption::VALUE_OPTIONAL, 'The terminal command that should be assigned', 'command:name'],
        ];
    }

    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        $command = $this->option('command') ?: Str::snake($this->argument('name'), '-');

        return str_replace('{{ command }}', $command, $stub);
    }
}
