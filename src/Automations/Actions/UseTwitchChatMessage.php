<?php

namespace Redbeed\OpenOverlay\Automations\Actions;

use Illuminate\Foundation\Auth\User;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\Models\BotConnection;

trait UseTwitchChatMessage
{
    private ChatMessage $chatMessage;

    private ?BotConnection $botConnection = null;

    private ?User $user = null;

    public function setChatMessage(ChatMessage $chatMessage): void
    {
        $this->chatMessage = $chatMessage;
    }

    public function setBotConnection(BotConnection $botConnection): void
    {
        $this->botConnection = $botConnection;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    protected function getBot(): ?BotConnection
    {
        return $this->botConnection ?: $this->chatMessage?->bot;
    }

    protected function getUser(): ?User
    {
        return $this->user ?: $this->chatMessage?->channelUser;
    }
}
