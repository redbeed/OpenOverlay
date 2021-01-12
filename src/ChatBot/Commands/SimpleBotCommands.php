<?php


namespace Redbeed\OpenOverlay\ChatBot\Commands;


use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;

class SimpleBotCommands extends BotCommand
{
    /** @var string[] */
    public $simpleCommands;

    public function __construct(ConnectionHandler $connectionHandler)
    {
        parent::__construct($connectionHandler);

        $this->simpleCommands = config('openoverlay.bot.commands.simple');
    }

    public function handle(ChatMessage $chatMessage)
    {
        foreach ($this->simpleCommands as $command => $responseMessage) {
            $this->handleSimpleCommand($chatMessage, $command, $responseMessage);
        }
    }

    public function handleSimpleCommand(ChatMessage $chatMessage, string $command, string $responseMessage): void
    {
        if ($this->messageStartsWith($chatMessage->message, $command) === false) {
            return;
        }

        $this->connection->sendChatMessage(
            $chatMessage->channel,
            $this->responseSimpleCommand($chatMessage, $responseMessage)
        );
    }

    public function responseSimpleCommand(ChatMessage $chatMessage, string $message): string
    {
        $replace = [
            '%username%' => $chatMessage->username,
        ];

        return str_replace(array_keys($replace), array_values($replace), $message);
    }
}
