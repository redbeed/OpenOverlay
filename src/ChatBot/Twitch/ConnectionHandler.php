<?php


namespace Redbeed\OpenOverlay\ChatBot\Twitch;

use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Commands\BotCommand;
use Redbeed\OpenOverlay\ChatBot\Commands\SimpleBotCommands;
use Redbeed\OpenOverlay\Events\Twitch\BotTokenExpires;
use Redbeed\OpenOverlay\Events\Twitch\ChatMessageReceived;
use Redbeed\OpenOverlay\Models\BotConnection;
use Redbeed\OpenOverlay\Models\Twitch\Emote;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\ChatEmotesClient;

class ConnectionHandler
{
    const TWITCH_IRC_URL = 'wss://irc-ws.chat.twitch.tv:443';

    /** @var WebSocket */
    private $connection;

    /** @var BotConnection */
    private $bot;

    /** @var BotCommand[] */
    private $customCommands = [];

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

        $this->connection->on('message', function ($message) use ($connection) {
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
        if (strpos($message, 'PRIVMSG') !== false) {
            $this->chatMessageReceived($message);
        }
    }

    public function basicMessageHandler(string $message): void
    {
        // ignore for basic handler
        if (strpos($message, 'PRIVMSG') !== false) {
            return;
        }

        // get join message
        if (strpos($message, 'NOTICE * :Login authentication failed') !== false) {
            $this->write("LOGIN | " . $message);
            event(new BotTokenExpires($this->bot));

            $this->connection->close();
            return;
        }

        // get join message
        if (strpos($message, 'PING') !== false) {
            $this->pingReceived($message);

            return;
        }

        // get join message
        if (strpos($message, 'JOIN') !== false) {
            $this->joinMessageReceived($message);

            return;
        }

        $this->write("UNKOWN | " . $message . PHP_EOL);
    }

    public function pingReceived(string $message): void
    {
        $this->send('PONG :tmi.twitch.tv');
        $this->write("PING PONG done");
    }

    public function joinMessageReceived(string $message): void
    {
        try {
            preg_match("/:(.*)\!.*#(.*)/", $message, $matches);

            $this->write("BOT (" . $matches[1] . ") joined " . $matches[2]);

            $channelName = trim(strtolower($matches[2]));

            $this->joinedChannel[] = $channelName;
            $this->runChannelQueue($channelName);

            if (isset($this->joinedCallBack[$channelName])) {
                $this->write("   -> callback started");
                $this->joinedCallBack[$channelName]();
            }

        } catch (\Exception $exception) {
            $this->write($exception->getMessage() . ' ' . $exception->getLine() . PHP_EOL);
        }
    }

    public function addJoinedCallBack(string $channelName, callable $callback): void
    {
        $channelName = strtolower($channelName);
        $this->write('HELLOP! ' . $channelName);

        $this->joinedCallBack[$channelName] = $callback;
    }

    public function chatMessageReceived(string $message): void
    {
        $model = ChatMessage::parseIRCMessage($message);

        if ($model === null) {
            return;
        }

        $model->possibleEmotes = $this->emoteSets[$model->channel] ?? [];

        $this->write($model->channel . ' | ' . $model->username . ': ' . $model->message);

        try {
            // Check commands
            foreach ($this->customCommands as $commandHandler) {
                $commandHandler->handle($model);
            }
        } catch (\Exception $exception) {
            $this->write($exception->getMessage());
            $this->write($exception->getFile() . ' #' . $exception->getLine());
        }

        $this->write($model->channel . ' | ' . $model->username . ': ' . $model->message . ' HANDLED');

        try {
            event(new ChatMessageReceived($model));
        } catch (\Exception $exception) {
            $this->write("  -> EVENT ERROR: " . $exception->getMessage());
        }
    }

    public function auth(BotConnection $bot)
    {
        $this->bot = $bot;

        $this->send('PASS oauth:' . $this->bot->service_token);
        $this->send('NICK ' . strtolower($this->bot->bot_username));
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

        $this->send('JOIN #' . strtolower($channelName));
        $this->write('JOIN #' . strtolower($channelName));
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

        if (!empty($this->channelQueue[$channelName])) {
            foreach ($this->channelQueue[$channelName] as $item) {
                $this->send($item);
            }
        }

        $this->channelQueue[$channelName] = [];
    }

    public function sendChatMessage(string $channelName, string $message): void
    {
        $lowerChannelName = strtolower($channelName);
        $message = 'PRIVMSG #' . $lowerChannelName . ' :' . $message . PHP_EOL;

        // send message after channel joined
        if (!in_array($lowerChannelName, $this->joinedChannel)) {
            $this->channelQueue[$lowerChannelName][] = $message;

            return;
        }

        $this->send($message);
        $this->write($message);
    }

    public function initCustomCommands(): void
    {
        /** @var BotCommand[] $commandClasses */
        $commandClasses = config('openoverlay.bot.commands.advanced');

        // add simple command handler
        $commandClasses[] = SimpleBotCommands::class;

        foreach ($commandClasses as $commandClass) {
            $this->customCommands[] = new $commandClass($this);
        }
    }

    protected function write(string $output, $newLine = true)
    {
        echo $output . ($newLine ? PHP_EOL : '');
    }

}
