<?php

namespace Redbeed\OpenOverlay\ChatBot\Twitch;

use Illuminate\Support\Facades\Log;
use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\Events\Twitch\BotTokenExpires;
use Redbeed\OpenOverlay\Events\Twitch\ChatMessageReceived;
use Redbeed\OpenOverlay\Models\BotConnection;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\ChatEmotesClient;

class ConnectionHandler
{
    const TWITCH_IRC_URL = 'wss://irc-ws.chat.twitch.tv:443';

    /** @var WebSocket */
    private $connection;

    /** @var BotConnection */
    private $bot;

    /** @var string[] */
    private $joinedChannel = [];

    /** @var array[] */
    private $channelQueue = [];

    /** @var mixed[] */
    private $joinedCallBack = [];

    /** @var array */
    private $emoteSets = [];

    public function __construct(WebSocket $connection)
    {
        $this->connection = $connection;

        $this->connection->on('message', function ($message) {
            $this->basicMessageHandler($message);
        });
    }

    public static function withPrivateMessageHandler(WebSocket $connection): ConnectionHandler
    {
        $connection = new self($connection);

        $connection->connection->on('message', function ($message) use ($connection) {
            $connection->privateMessageHandler($message);
        });

        return $connection;
    }

    public function privateMessageHandler(string $message): void
    {
        // if is chat message
        if (str_contains($message, 'PRIVMSG')) {
            $this->chatMessageReceived($message);
        }
    }

    public function basicMessageHandler(string $message): void
    {
        // if is chat message starts with PRIVMSG ignore basic handler
        if (str_contains($message, 'PRIVMSG')) {
            return;
        }

        // if this message contains "Login authentication" reset bot connection
        if (str_contains($message, 'NOTICE * :Login authentication failed')) {
            $this->write('LOGIN | '.$message);
            event(new BotTokenExpires($this->bot));

            $this->connection->close();

            return;
        }

        // handle ping message from twitch
        if (str_contains($message, 'PING')) {
            $this->pingReceived($message);

            return;
        }

        // handle join confirmation
        if (str_contains($message, 'JOIN')) {
            $this->joinMessageReceived($message);

            return;
        }

        $this->write('UNKOWN | '.$message.PHP_EOL, '');
    }

    public function pingReceived(string $message): void
    {
        $this->send('PONG :tmi.twitch.tv');
        $this->write('PING PONG done');
    }

    public function joinMessageReceived(string $message): void
    {
        try {
            preg_match("/:(.*)\!.*#(.*)/", $message, $matches);

            $this->write('BOT ('.$matches[1].') joined '.$matches[2]);

            $channelName = trim(strtolower($matches[2]));

            $this->joinedChannel[] = $channelName;
            $this->runChannelQueue($channelName);

            $this->afterJoinCallBacks($channelName);
        } catch (\Exception $exception) {
            $this->write($exception->getMessage().' '.$exception->getLine().PHP_EOL, 'ERROR');
        }
    }

    private function afterJoinCallBacks(string $channelName)
    {
        $channelName = strtolower($channelName);

        if (isset($this->joinedCallBack[$channelName])) {
            $this->write('CALL CALLBACK FOR '.$channelName);
            $this->joinedCallBack[$channelName]();
        }
    }

    public function addJoinedCallBack(string $channelName, callable $callback): void
    {
        $channelName = strtolower($channelName);

        $this->joinedCallBack[$channelName] = $callback;
        $this->write('Callback added for '.$channelName);

        // channel already joined
        if (in_array($channelName, $this->joinedChannel)) {
            $this->afterJoinCallBacks($channelName);
        }
    }

    public function chatMessageReceived(string $message): void
    {
        $model = ChatMessage::parseIRCMessage($this->bot, $message);

        if ($model === null) {
            return;
        }

        $model->possibleEmotes = $this->emoteSets[$model->channel] ?? [];

        $this->write($model->channel.' | '.$model->username.': '.$model->message, 'Twitch');

        try {
            event(new ChatMessageReceived($model));
        } catch (\Exception $exception) {
            Log::error($exception);
            $this->write('  -> EVENT ERROR: '.$exception->getMessage(), 'ERROR');
        }
    }

    public function auth(BotConnection $bot)
    {
        $this->bot = $bot;

        $this->send('PASS oauth:'.$this->bot->service_token);
        $this->send('NICK '.strtolower($this->bot->bot_username));
    }

    public function send(string $message): void
    {
        $this->connection->send($message);
    }

    public function joinChannel(Connection $channel): void
    {
        $channelName = strtolower($channel->service_username);

        $this->channelQueue[$channelName] = [];
        $this->loadEmotes($channel);

        $this->send('JOIN #'.strtolower($channelName));
        $this->write('JOIN #'.strtolower($channelName));
    }

    private function loadEmotes(Connection $channel)
    {
        $emoteClient = new ChatEmotesClient();
        $channelName = strtolower($channel->service_username);

        $this->emoteSets[$channelName] = collect($emoteClient->get($channel->service_user_id))
            ->merge($emoteClient->global())
            ->merge($emoteClient->allSets())
            ->toArray();
    }

    private function runChannelQueue(string $channelName): void
    {
        $channelName = trim(strtolower($channelName));

        if (! empty($this->channelQueue[$channelName])) {
            foreach ($this->channelQueue[$channelName] as $item) {
                $this->send($item);
            }
        }

        $this->channelQueue[$channelName] = [];
    }

    public function sendChatMessage(string $channelName, string $message): void
    {
        $lowerChannelName = strtolower($channelName);
        $message = 'PRIVMSG #'.$lowerChannelName.' :'.$message.PHP_EOL;

        // send message after channel joined
        if (! in_array($lowerChannelName, $this->joinedChannel)) {
            $this->channelQueue[$lowerChannelName][] = $message;

            return;
        }

        $this->send($message);
        $this->write($message);
    }

    protected function write(string $output, string $title = 'OpenOverlay', $newLine = true)
    {
        $title = ! empty($title) ? '['.$title.']' : '';
        echo trim($title.' '.$output).($newLine ? PHP_EOL : '');
    }
}
