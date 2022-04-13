<?php

namespace Redbeed\OpenOverlay\Automations\Filters\Twitch\ChatMessage;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;
use Redbeed\OpenOverlay\Automations\Filters\Filter;
use Redbeed\OpenOverlay\Automations\Triggers\TwitchChatMessageTrigger;
use Redbeed\OpenOverlay\Exceptions\AutomationFilterNotValid;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;

class ChatMessageContainsWithPatternFilter extends Filter
{
    public static string $name = 'Chat message contains';
    public static string $description = 'Filter chat messages by string  ';

    private string $needle;
    private bool $caseSensitive;

    /** @var string[] */
    private array $regexPatterns;

    /**
     * @var TwitchChatMessageTrigger
     */
    protected $trigger;


    public function __construct(string $needle, array $regexPatterns, bool $needleCaseSensitive = false)
    {
        $this->needle = $needle;
        $this->regexPatterns = $regexPatterns;
        $this->caseSensitive = $needleCaseSensitive;
    }

    public function validate(): bool
    {
        $message = $this->trigger->message->message;
        $needle = $this->needle;

        if (!$this->caseSensitive) {
            $message = Str::lower($message);
            $needle = Str::lower($needle);
        }

        if (!Str::contains($message, $needle)) {
            return false;
        }

        return preg_match($this->regex(), $this->trigger->message->message);
    }

    private function regex(): string
    {
        $regex = collect($this->regexPatterns)
            ->mapWithKeys(function ($regex, $key) {
                return [$key => '(?<' . $key . '>' . $regex . ')'];
            })
            ->prepend($this->needle)
            ->implode(' ');

        return '/' . $regex . '/';
    }

    private function matches(): array
    {
        $matches = [];
        preg_match($this->regex(), $this->trigger->message->message, $matches);

        return array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
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
        return array_merge_recursive([
            'username' => $this->trigger->message->username,
            'game'     => function () {
                try {
                    return (new UsersClient())->lastGame($this->trigger->message->username);
                } catch (ClientException) {
                    return '';
                }
            },
        ], $this->matches());
    }
}
