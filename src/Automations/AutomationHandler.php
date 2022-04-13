<?php

namespace Redbeed\OpenOverlay\Automations;

use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use Redbeed\OpenOverlay\Automations\Actions\UsesVariables;
use Redbeed\OpenOverlay\Automations\Filters\Filter;

class AutomationHandler
{

    private array $trigger = [];

    public function __construct($trigger)
    {
        $this->trigger = $trigger;
    }

    /**
     * @return Filter[]
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        return [];
    }

    public function handle()
    {
        $variables = [];

        foreach ($this->filters() as $filter) {
            $response = $filter->handle($this->trigger);

            if ($response === false) {
                // Filter failed stop the automation
                return;
            }

            $variables = array_merge_recursive(
                $variables, $filter->variables()
            );
        }

        foreach ($this->actions() as $action) {
            $traits = class_uses($action);

            // Check if the action uses the UsesVariables trait and if so, add the variables to the action
            if (in_array(UsesVariables::class, $traits)) {
                $action->addVariables($variables);
            }

            $action->handle();
        }
    }

    #[ArrayShape(['trigger' => "string", 'options' => "array"])]
    public static function triggerConfig(string $triggerClass, array $options = [])
    {
        return ['trigger' => $triggerClass, 'options' => $options];
    }

    #[ArrayShape(['action' => "string", 'options' => "array"])]
    public static function actionConfig(string $actionClass, array $options = [])
    {
        return ['action' => $actionClass, 'options' => $options];
    }
}
