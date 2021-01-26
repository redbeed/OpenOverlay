<?php

namespace Redbeed\OpenOverlay\ChatBot\Commands;

use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;

class BotCommand
{
    /** @var ConnectionHandler */
    protected $connection;

    /** @var string[] */
    private $parameters = [];

    /** @var string */
    public $signature = '';

    /** @var string[] */
    public $aliasCommands = [];

    public function __construct(ConnectionHandler $connectionHandler)
    {
        $this->connection = $connectionHandler;
    }

    public function handle(ChatMessage $chatMessage)
    {
        if ($this->messageValid($chatMessage->message) === false) {
            return;
        }

        // build & check parameters
        $this->buildParameters($chatMessage->message);
        if ($this->parametersValid() === false) {
            return;
        }

        $this->connection->sendChatMessage(
            $chatMessage->channel,
            $this->response($chatMessage)
        );
    }

    protected function parametersValid(): bool
    {
        $keys = $this->parametersKeys();

        return count($keys) === count($this->parameters);
    }

    protected function buildParameters(string $message)
    {
        $keys = $this->parametersKeys();
        if (count($keys) <= 0) {
            return;
        }

        $valuesOnly = explode(' ', $message, 2);
        if (count($valuesOnly) !== 2) {
            return;
        }

        $values = explode(' ', $valuesOnly[1], count($keys));
        foreach ($values as $valueKey => $value) {
            $this->parameters[$keys[$valueKey]] = $value;
        }
    }

    protected function parameter(string $key): ?string
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        return null;
    }

    public function parametersKeys(): array
    {
        preg_match_all("/\{(.+?)\}/m", $this->signature, $matches);
        if (count($matches) < 2) {
            return [];
        }

        return $matches[1];
    }

    public function response(ChatMessage $chatMessage): string
    {
        return '';
    }

    protected function command(): string
    {
        return head(explode(' ', $this->signature));
    }

    protected function messageValid(string $message): bool
    {
        if ($this->messageStartsWith($message, $this->command())) {
            return true;
        }

        if (is_array($this->aliasCommands) && count($this->aliasCommands)) {
            foreach ($this->aliasCommands as $aliasCommand) {
                if ($this->messageStartsWith($message, $aliasCommand)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function messageStartsWith(string $message, string $command): bool
    {
        // perfect match
        if (trim($command) === trim($message)) {
            return true;
        }

        // match with space
        return substr(trim($message), 0, strlen(trim($command) . ' ')) === trim($command) . ' ';
    }
}
