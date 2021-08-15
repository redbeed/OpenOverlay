<?php

namespace Redbeed\OpenOverlay\Support;

use Illuminate\Support\Facades\Cache;
use Redbeed\OpenOverlay\Events\ViewerEnteredChat;
use Redbeed\OpenOverlay\Models\User\Connection;

class ViewerInChat
{
    private static function cacheKey(Connection $streamer, string $platform = 'twitch'): string
    {
        return $platform . '.streamer.' . $streamer->service_user_id . '.viewer.' . StreamerOnline::onlineTime($streamer->service_user_id, $platform);
    }

    public static function list(Connection $streamer, string $platform = 'twitch'): array
    {
        return Cache::get(self::cacheKey($streamer, $platform), []);
    }

    public static function add(string $username, Connection $streamer, string $platform = 'twitch')
    {
        $users = self::list($streamer, $platform);

        if (in_array($username, $users)) {
            return;
        }

        $users[] = $username;
        Cache::put(self::cacheKey($streamer, $platform), $users);

        broadcast(new ViewerEnteredChat($username, $streamer));
    }

    public static function clear(Connection $streamer, string $platform = 'twitch')
    {
        Cache::pull(self::cacheKey($streamer, $platform));
    }
}
