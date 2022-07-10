<?php

namespace Redbeed\OpenOverlay\Automations\Filters\ChatMessage;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Redbeed\OpenOverlay\Automations\Filters\Filter;
use Redbeed\OpenOverlay\Automations\Triggers\TwitchChatMessageTrigger;
use Redbeed\OpenOverlay\Exceptions\AutomationFilterNotValid;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;

class ChatMessageContainsFilter extends Filter
{
    public static string $name = 'Chat message check';

    public static string $description = 'Filter chat message by string.';

    private string $needle;

    private bool $caseSensitive;

    /**
     * @var TwitchChatMessageTrigger
     */
    protected $trigger;

    public function __construct(string $needle, bool $caseSensitive = false)
    {
        $this->needle = $needle;
        $this->caseSensitive = $caseSensitive;
    }

    #[Pure]
    public function validate(): bool
    {
        $message = $this->trigger->message->message;
        $needle = $this->needle;

        if (! $this->caseSensitive) {
            $message = Str::lower($message);
            $needle = Str::lower($needle);
        }

        return Str::contains($message, $needle);
    }

    /**
     * @throws AutomationFilterNotValid
     */
    public function validTrigger()
    {
        parent::validTrigger();

        if (! ($this->trigger instanceof TwitchChatMessageTrigger)) {
            throw new AutomationFilterNotValid('Trigger is not valid. Trigger must be instance of TwitchChatMessageTrigger but is '.get_class($this->trigger));
        }
    }

    public function variables(): array
    {
        return [
            'username' => $this->trigger->message->username,
            'twitchUrl' => 'https://www.twitch.tv/'.$this->trigger->message->username,
            'game' => function () {
                try {
                    return (new UsersClient())->lastGame($this->trigger->message->username);
                } catch (ClientException) {
                    return '';
                }
            },
        ];
    }

    public function settings(): array
    {
        return [
            'needle' => $this->needle,
            'caseSensitive' => $this->caseSensitive,
        ];
    }
}
