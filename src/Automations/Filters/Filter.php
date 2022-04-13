<?php

namespace Redbeed\OpenOverlay\Automations\Filters;

use Redbeed\OpenOverlay\Automations\Triggers\Trigger;
use Redbeed\OpenOverlay\Exceptions\AutomationFilterNotValid;

abstract class Filter
{

    static public string $name;
    static public string $description;

    /**
     * @var Trigger|mixed
     */
    protected $trigger;

    /**
     * @param Trigger $trigger
     * @return bool
     * @throws AutomationFilterNotValid
     */
    public function handle(mixed $trigger): bool
    {
        $this->trigger = $trigger;
        $this->validTrigger();

        return $this->validate();
    }

    public function validate(): bool
    {
        return true;
    }

    /**
     * @throws AutomationFilterNotValid
     */
    public function validTrigger()
    {
        if (!($this->trigger instanceof Trigger)) {
            throw new AutomationFilterNotValid('Trigger is not valid. Trigger must be instance of Trigger but is ' . get_class($this->trigger));
        }
    }

    public function variables(): array
    {
        return [];
    }
}
