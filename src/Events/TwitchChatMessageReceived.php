<?php

namespace Redbeed\OpenOverlay\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\Models\User\Connection;

class TwitchChatMessageReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var ChatMessage */
    public $message;

    public $twitchUser;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
        $this->twitchUser = Connection::where('service_username', $this->message->channel)->first();
    }

//    public function broadcastWhen()
//    {
//        return $this->twitchUser !== null;
//    }

    public function broadcastOn(): Channel
    {
        return new Channel('twitch.'.$this->twitchUser->service_user_id);
    }

    public function broadcastAs(): string
    {
        return 'chat-message-received';
    }

    public function broadcastWith()
    {
        return [
            'username' => $this->message->username,
            'message' => $this->message->message,
            'channel' => $this->message->channel,
        ];
    }
}
