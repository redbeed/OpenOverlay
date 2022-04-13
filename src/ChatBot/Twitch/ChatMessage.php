<?php

namespace Redbeed\OpenOverlay\ChatBot\Twitch;

use http\Client\Curl\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Redbeed\OpenOverlay\Models\BotConnection;
use Redbeed\OpenOverlay\Models\Twitch\Emote;
use Redbeed\OpenOverlay\Models\User\Connection;

class ChatMessage
{
    public string $username;

    public string $channel;

    public string $message;

    /** @var Emote[] */
    public array $possibleEmotes;

    public ?\Illuminate\Foundation\Auth\User $channelUser;
    public ?BotConnection $bot;

    public function __construct(string $channel, string $username, string $message, ?BotConnection $bot = null)
    {
        $this->channel = $channel;
        $this->username = trim($username);
        $this->message = trim($message);

        $this->bot = $bot;
        if ($this->bot) {
            $this->channelUser = $this->bot->users()->where('name', $this->channel)->first();
        }
    }

    public static function parseIRCMessage(BotConnection $bot, string $message): ?ChatMessage
    {
        try {
            preg_match("/:(.*)\!.*#(\S+) :(.*)/", $message, $matches);
            $message = new ChatMessage($matches[2], $matches[1], $matches[3], $bot);

            return $message;
        } catch (\Exception $exception) {
            Log::error($exception);
        }

        return null;
    }

    public function toHtml(string $emoteSize = Emote::IMAGE_SIZE_MD): string
    {
        $emoteList = collect($this->possibleEmotes)
            ->map(function (Emote $emote) use ($emoteSize) {
                $name = htmlspecialchars_decode($emote->name);
                $regex = '/' . preg_quote($name, '/') . '(\s|$)/';

                if (@preg_match($regex, null) === false) {
                    echo "Emote Regex '" . $regex . "' is invalid \r\n";
                    return null;
                }

                return [
                    'name'  => $regex,
                    'image' => '<img src="' . $emote->image($emoteSize) . '" class="twitch-emote" alt="' . Str::slug($emote->name) . '"> ',
                ];
            });

        return preg_replace(
            $emoteList->pluck('name')->filter()->toArray(),
            $emoteList->pluck('image')->filter()->toArray(),
            $this->message
        );
    }
}
