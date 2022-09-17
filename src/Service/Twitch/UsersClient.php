<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Redbeed\OpenOverlay\Exceptions\AppTokenMissing;

class UsersClient extends ApiClient
{
    /**
     * @throws AppTokenMissing
     * @throws GuzzleException
     */
    public function byId(string $id): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'id' => $id,
                ],
            ])
            ->request('GET', 'users');
    }

    /**
     * @throws AppTokenMissing
     * @throws GuzzleException
     */
    public function byUsername(string $username): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'login' => $username,
                ],
            ])
            ->request('GET', 'users');
    }

    /**
     * @throws AppTokenMissing
     * @throws GuzzleException
     */
    public function followers(string $twitchUserId): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'to_id' => $twitchUserId,
                ],
            ])
            ->request('GET', 'users/follows');
    }

    /**
     * @throws AppTokenMissing
     * @throws GuzzleException
     */
    public function allFollowers(string $twitchUserId): array
    {
        $firstResponse = $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'first' => 100,
                ],
            ])
            ->followers($twitchUserId);

        $totalFollowers = $firstResponse['total'];
        $followers = $firstResponse['data'];
        $paginationCursor = $firstResponse['pagination']['cursor'] ?? null;

        while ($totalFollowers > count($followers) || $paginationCursor !== null) {
            $response = $this
                ->addAppToken()
                ->withOptions([
                    RequestOptions::QUERY => [
                        'first' => 100,
                        'after' => $paginationCursor,
                    ],
                ])
                ->followers($twitchUserId);

            $paginationCursor = $response['pagination']['cursor'] ?? null;
            $followers = array_merge($followers, $response['data'] ?? []); // @todo: replace array_merge because its slow
        }

        $firstResponse['data'] = $followers;
        $firstResponse['pagination'] = [];

        return $firstResponse;
    }

    /**
     * @throws AppTokenMissing
     * @throws GuzzleException
     */
    public static function lastGame(string $username): string
    {
        $users = (new self)->byUsername($username);
        if (empty($users['data']) || count($users['data']) === 0) {
            return '';
        }

        $user = Arr::first($users['data']);
        if (empty($user) || empty($user['id'])) {
            return '';
        }

        return (new ChannelsClient())->lastGame($user['id']);
    }
}
