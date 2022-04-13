<?php

namespace Redbeed\OpenOverlay\Automations\Triggers;

abstract class Trigger
{
    static protected string $name;

    static protected string $description;

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
