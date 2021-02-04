<?php

namespace Redbeed\OpenOverlay\Models\Twitch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFollowers extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'twitch_user_followers';

    protected $fillable = [
        'twitch_user_id',
        'follower_user_id', 'follower_username',
        'followed_at',
    ];

    protected $casts = [
        'followed_at' => 'datetime',
    ];
}
