<?php

namespace Redbeed\OpenOverlay\Models\Twitch;

use Carbon\Carbon;
use Redbeed\OpenOverlay\Service\Twitch\DateTime;

class EventSubscription
{
    /** @var string */
    public $id;

    /** @var string */
    public $status;

    /** @var string */
    public $type;

    /** @var int */
    public $version;

    /** @var array */
    public $condition;

    /** @var Carbon */
    public $createdAt;

    /** @var array */
    public $transport;

    public static function createFromTwitch(array $twitchData): self
    {
        $model = new self();

        $model->id = $twitchData['id'];
        $model->status = $twitchData['status'];
        $model->type = $twitchData['type'];
        $model->version = $twitchData['version'];
        $model->condition = $twitchData['condition'];
        $model->transport = $twitchData['transport'];
        $model->createdAt = DateTime::parse($twitchData['created_at']);

        return $model;
    }
}
