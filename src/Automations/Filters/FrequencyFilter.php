<?php

namespace Redbeed\OpenOverlay\Automations\Filters;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Redbeed\OpenOverlay\Automations\Triggers\ScheduleTrigger;
use Redbeed\OpenOverlay\Exceptions\AutomationFilterNotValid;

class FrequencyFilter extends Filter
{
    use ManagesFrequencies;

    public string $expression = '* * * * *';

    /**
     * @throws AutomationFilterNotValid
     */
    public function validTrigger()
    {
        parent::validTrigger();

        if (! ($this->trigger instanceof ScheduleTrigger)) {
            throw new AutomationFilterNotValid('Trigger is not valid. Trigger must be instance of ScheduleTrigger but is '.get_class($this->trigger));
        }
    }

    public function validate(): bool
    {
        return (new CronExpression($this->expression))->isDue($this->trigger->date->toDateTimeString());
    }
}
