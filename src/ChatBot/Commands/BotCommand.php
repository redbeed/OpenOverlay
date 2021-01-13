<?php

namespace Redbeed\OpenOverlay\ChatBot\Commands;

use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;

class BotCommand
{
    /** @var ConnectionHandler */
    protected $connection;

    /** @var string */
    public $command;

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

        $this->connection->sendChatMessage(
            $chatMessage->channel,
            $this->response($chatMessage)
        );
    }

    public function response(ChatMessage $chatMessage): string
    {
        return '';
    }

    protected function messageValid(string $message): bool
    {
        if ($this->messageStartsWith($message, $this->command)) {
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
