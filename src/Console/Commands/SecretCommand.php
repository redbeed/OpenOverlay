<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use Illuminate\Support\Str;

class SecretCommand extends EventSubListingCommand
{

    private const ENV_KEY = 'OVERLAY_SECRET';

    protected $signature = 'overlay:secret
                    {--show : Show the Secret that will be set}
                    {--force : Force replacing secret key}';

    protected $description = 'Generate new secret for safer twitch communication';

    public function handle(): void
    {
        $currentSecret = env(self::ENV_KEY);

        if (!empty($currentSecret) && !$this->option('force')) {
            $this->warn('You already have a secret');
            return;
        }

        $secretKey = $this->generateSecretKey();

        $this->showOption($secretKey);
        $this->writeSecretKeyInEnvironmentFile($secretKey);
    }

    private function generateSecretKey(): string
    {
        return Str::random(20);
    }

    private function writeSecretKeyInEnvironmentFile($key): void
    {
        $envFilePath = $this->laravel->environmentFilePath();
        $envFileContent = file_get_contents($envFilePath);
        $secretKeyPattern = $this->keyReplacementPattern();

        if (preg_match($secretKeyPattern, $envFileContent) === 0) {
            file_put_contents(
                $envFilePath,
                self::ENV_KEY . '=' . $key . PHP_EOL,
                FILE_APPEND
            );

            $this->info('New Secret Key added');

            return;
        }

        file_put_contents(
            $envFilePath,
            preg_replace(
                $secretKeyPattern,
                self::ENV_KEY . '=' . $key,
                $envFileContent
            )
        );

        $this->info('New Secret Key replaced');
    }

    private function showOption(string $key): void
    {
        if ($this->option('show')) {
            $this->info('New Secret Key:');
            $this->info(self::ENV_KEY . '=' . $key);
        }
    }

    protected function keyReplacementPattern(): string
    {
        $escaped = preg_quote('=' . env(self::ENV_KEY, ''), '/');
        return "/^" . self::ENV_KEY . "{$escaped}/m";
    }
}
