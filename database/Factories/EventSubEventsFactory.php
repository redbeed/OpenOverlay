<?php

namespace Redbeed\OpenOverlay\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;

class EventSubEventsFactory extends Factory
{
    protected $model = EventSubEvents::class;

    /**
     * {@inheritDoc}
     */
    public function definition()
    {
        return [
            'event_id' => $this->faker->unique()->uuid,
            'event_type' => '',
            'event_user_id' => $this->faker->unique()->uuid,
            'event_data' => [],
            'event_sent' => now(),
        ];
    }
}
