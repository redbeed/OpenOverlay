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

    public static string $name = 'Chat message contains';
    public static string $description = 'Filter chat messages by string  ';

    private string $needle;

    /**
     * @var TwitchChatMessageTrigger
     */
    protected $trigger;

    public function __construct(string $needle)
    {
        $this->needle = $needle;
    }

    #[Pure]
    public function validate(): bool
    {
        return Str::contains($this->trigger->message->message, $this->needle);
    }

    /**
     * @throws AutomationFilterNotValid
     */
    public function validTrigger()
    {
        parent::validTrigger();

        if (!($this->trigger instanceof TwitchChatMessageTrigger)) {
            throw new AutomationFilterNotValid('Trigger is not valid. Trigger must be instance of TwitchChatMessageTrigger but is ' . get_class($this->trigger));
        }
    }

    public function variables(): array
    {
        return [
            'username'  => $this->trigger->message->username,
            'twitchUrl' => 'https://www.twitch.tv/' . $this->trigger->message->username,
            'game'      => function () {
                try {
                    return (new UsersClient())->lastGame($this->trigger->message->username);
                } catch (ClientException) {
                    return '';
                }
            },
        ];
    }
}
