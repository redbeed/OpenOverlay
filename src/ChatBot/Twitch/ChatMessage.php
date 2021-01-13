<?php


namespace Redbeed\OpenOverlay\ChatBot\Twitch;

class ChatMessage
{
    /** @var string */
    public $username;

    /** @var string */
    public $channel;

    /** @var string */
    public $message;

    public function __construct(string $channel, string $username, string $message)
    {
        $this->channel = $channel;
        $this->username = $username;
        $this->message = $message;
    }

    public static function parseIRCMessage(string $message): ?ChatMessage
    {
        try {
            preg_match("/:(.*)\!.*#(.*) :(.*)/", $message, $matches);

            return new ChatMessage($matches[2], $matches[1], $matches[3]);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return null;
    }
}
