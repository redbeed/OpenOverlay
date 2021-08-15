<?php

namespace Redbeed\OpenOverlay\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StreamerOnline
{
    private static function cacheKey(string $streamerId, string $platform = 'twitch'): string
    {
        return $platform . '.streamer.' . $streamerId . '.online.';
    }

    public static function onlineTime(string $streamerId, string $platform = 'twitch'): ?Carbon
    {
        $date = Cache::get(self::cacheKey($streamerId, $platform));

        if ($date instanceof Carbon) {
            return $date;
        }

        return null;
    }

    public static function isOnline(string $streamerId, string $platform = 'twitch'): bool
    {
        $date = self::onlineTime($streamerId, $platform);

        return $date !== null;
    }

    public static function setOnline(string $streamerId, $date, string $platform = 'twitch')
    {
        Cache::put(
            self::cacheKey($streamerId, $platform),
            Carbon::parse($date)
        );
    }

    public static function setOffline(string $streamerId, string $platform = 'twitch')
    {
        Cache::pull(self::cacheKey($streamerId, $platform));
    }

}
