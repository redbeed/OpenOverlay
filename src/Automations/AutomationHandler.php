<?php

namespace Redbeed\OpenOverlay\Automations;

use JetBrains\PhpStorm\ArrayShape;
use Redbeed\OpenOverlay\Automations\Actions\UseTwitchChatMessage;
use Redbeed\OpenOverlay\Automations\Actions\UseVariables;
use Redbeed\OpenOverlay\Automations\Filters\Filter;
use Redbeed\OpenOverlay\Automations\Triggers\Trigger;
use Redbeed\OpenOverlay\Automations\Triggers\TwitchChatMessageTrigger;

class AutomationHandler
{
    public static string $name = 'Automation Handler';
    public static string $description = 'Will run filters on trigger and execute actions';

    /** @var Trigger */
    private $trigger;

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
            if (in_array(UseVariables::class, $traits)) {
                $action->addVariables($variables);
            }

            // Check if the action uses the UsesTwitchChatMessage trait and if so, add the message to the action
            if (in_array(UseTwitchChatMessage::class, $traits) && $this->trigger instanceof TwitchChatMessageTrigger) {
                $action->setChatMessage($this->trigger->message);
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
