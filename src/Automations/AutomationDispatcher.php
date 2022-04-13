<?php

namespace Redbeed\OpenOverlay\Automations;

class AutomationDispatcher
{
    protected array $automations = [];

    public function add(string $trigger, string|array $handlerClass)
    {
        if (is_array($handlerClass)) {
            return collect($handlerClass)->each(function ($handler) use ($trigger) {
                $this->add($trigger, $handler);
            });
        }

        $this->automations[$trigger][] = $handlerClass;
    }

    public function trigger(mixed $trigger)
    {
        if (empty($this->automations[$trigger::class])) {
            return;
        }

        foreach ($this->automations[$trigger::class] as $automations) {
            collect($automations)->each(function ($automation) use ($trigger) {
                $automation = new $automation($trigger);
                $automation->handle();
            });
        }
    }
}
