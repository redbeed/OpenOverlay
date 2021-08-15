<?php

namespace Redbeed\OpenOverlay\Models\Twitch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Redbeed\OpenOverlay\Database\Factories\EventSubEventsFactory;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\OpenOverlay;

class EventSubEvents extends Model
{
    use HasFactory;

    protected $table = 'twitch_event_sub_events';

    protected $fillable = [
        'event_id',
        'event_type', 'event_user_id',
        'event_data', 'event_sent',
    ];

    protected $casts = [
        'event_sent' => 'datetime',
        'event_data' => 'array',
    ];

    protected static function newFactory()
    {
        return EventSubEventsFactory::new();
    }
}
