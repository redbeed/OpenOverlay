<?php

namespace Redbeed\OpenOverlay\ChatBot\Twitch;

use Redbeed\OpenOverlay\Models\Twitch\Emote;

class ChatMessage
{
    /** @var string */
    public $username;

    /** @var string */
    public $channel;

    /** @var string */
    public $message;

    /** @var Emote[] */
    public $possibleEmotes;

    public function __construct(string $channel, string $username, string $message)
    {
        $this->channel = $channel;
        $this->username = trim($username);
        $this->message = trim($message);
    }

    public static function parseIRCMessage(string $message): ?ChatMessage
    {
        try {
            preg_match("/:(.*)\!.*#(\S+) :(.*)/", $message, $matches);

            return new ChatMessage($matches[2], $matches[1], $matches[3]);
        } catch (\Exception $exception) {
            echo $exception->getMessage()."\r\n";
        }

        return null;
    }

    public function toHtml(string $emoteSize = Emote::IMAGE_SIZE_MD): string
    {
        $emoteList = collect($this->possibleEmotes)
            ->map(function (Emote $emote) use ($emoteSize) {
                $name = htmlspecialchars_decode($emote->name);
                $regex = '/' . str_replace('\\\\', '\\\\\\', $name) . '/';

                if (@preg_match($regex, null) === false) {
                    echo "Emote Regex '" . $regex . "' is invalid \r\n";
                    return null;
                }

                return [
                    'name' => $regex,
                    'image' => '<img src="' . $emote->image($emoteSize) . '" class="twitch-emote" alt="' . $emote->name . '">',
                ];
            });

        return preg_replace(
            $emoteList->pluck('name')->filter()->toArray(),
            $emoteList->pluck('image')->filter()->toArray(),
            $this->message
        );
    }
}
