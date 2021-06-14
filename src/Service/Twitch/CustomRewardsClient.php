<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;
use Redbeed\OpenOverlay\Models\User\Connection;

class CustomRewardsClient extends ApiClient
{
    /** @var Connection */
    private $userConnection;

    public function __construct(Connection $userConnection)
    {
        parent::__construct();

        $this->userConnection = $userConnection;
    }

    public function get(string $broadcasterId): array
    {
        return $this
            ->withAppToken($this->userConnection->service_token)
            ->withOptions([
                RequestOptions::QUERY => [
                    'broadcaster_id' => $broadcasterId,
                ],
            ])
            ->request('GET', 'channel_points/custom_rewards');
    }

    public function getRedemptions(string $broadcasterId, string $rewardId): array
    {
        return $this
            ->withAppToken($this->userConnection->service_token)
            ->withOptions([
                RequestOptions::QUERY => [
                    'broadcaster_id' => $broadcasterId,
                    'reward_id' => $rewardId,
                ],
            ])
            ->request('GET', 'channel_points/custom_rewards/redemptions');
    }
}
