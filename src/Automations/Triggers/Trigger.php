<?php

namespace Redbeed\OpenOverlay\Automations\Triggers;

abstract class Trigger
{
    public static string $name;

    public static string $description;

    protected array $options = [];

    /**
     * Check if trigger is valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        return true;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
