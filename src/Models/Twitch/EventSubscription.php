<?php


namespace Redbeed\OpenOverlay\Models\Twitch;


use Carbon\Carbon;

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
        
        $timestamp = substr(trim($twitchData['created_at'], 'Z'), 0, 23) . 'Z';
        $model->createdAt = Carbon::createFromFormat(\DateTime::RFC3339_EXTENDED, $timestamp);

        return $model;
    }
}
