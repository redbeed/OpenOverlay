<?php

namespace Redbeed\OpenOverlay\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\Models\Twitch\Emote;
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
            'message_html' => $this->message->toHtml(Emote::IMAGE_SIZE_MD),
            'channel' => $this->message->channel,
        ];
    }
}
