<?php

namespace Redbeed\OpenOverlay\Models\Twitch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscriber extends Model
{
    use HasFactory;

    protected $table = 'twitch_user_subscribers';

    protected $fillable = [
        'twitch_user_id',
        'subscriber_user_id', 'subscriber_username',
        'tier', 'tier_name',
        'is_gift'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
    ];
}
