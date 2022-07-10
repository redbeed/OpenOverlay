<?php

namespace Redbeed\OpenOverlay\Models\Twitch;

class Emote
{
    const IMAGE_SIZE_SM = 'url_1x';

    const IMAGE_SIZE_MD = 'url_2x';

    const IMAGE_SIZE_LG = 'url_4x';

    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string[] */
    public $images;

    /** @var string */
    public $tier;

    /** @var string */
    public $emoteType;

    /** @var string */
    public $emoteSetId;

    public static function fromJson(array $emoteData): Emote
    {
        $emote = new Emote();

        $emote->id = $emoteData['id'];
        $emote->name = $emoteData['name'];
        $emote->images = $emoteData['images'];

        if (! empty($emoteData['tier'])) {
            $emote->tier = $emoteData['tier'];
        }

        if (! empty($emoteData['emote_type'])) {
            $emote->emoteType = $emoteData['emote_type'];
        }

        if (! empty($emoteData['emote_set_id'])) {
            $emote->emoteSetId = $emoteData['emote_set_id'];
        }

        return $emote;
    }

    public function image(string $size = Emote::IMAGE_SIZE_MD): string
    {
        return $this->images[$size];
    }
}
