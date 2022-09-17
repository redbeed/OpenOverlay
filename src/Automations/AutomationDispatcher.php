<?php

namespace Redbeed\OpenOverlay\Automations;

use Closure;

class AutomationDispatcher
{
    protected array $automations = [];

    public function add(string $trigger, string|array|Closure $handlerClass)
    {
        if (is_array($handlerClass)) {
            collect($handlerClass)->each(function ($handler) use ($trigger) {
                $this->add($trigger, $handler);
            });

            return;
        }

        $this->automations[$trigger][] = $handlerClass;
    }

    public function getAutomations(?string $triggerClass = null): array
    {
        if ($triggerClass) {
            return $this->automations[$triggerClass] ?? [];
        }

        return $this->automations;
    }

    public function trigger(mixed $trigger)
    {
        ray('fire '.get_class($trigger));

        if (empty($this->automations[$trigger::class])) {
            return;
        }

        foreach ($this->automations[$trigger::class] as $automations) {
            collect($automations)->each(function ($automation) use ($trigger) {
                // If it's a closure, execute it
                if ($automation instanceof Closure) {
                    $automation = $automation($trigger);
                    $automation->handle();

                    return;
                }

                $automation = new $automation($trigger);
                $automation->handle();
            });
        }
    }
}
