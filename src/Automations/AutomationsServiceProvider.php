<?php

namespace Redbeed\OpenOverlay\Automations;

use Illuminate\Support\ServiceProvider;
use Redbeed\OpenOverlay\Support\Facades\Automation;

class AutomationsServiceProvider extends ServiceProvider
{
    protected array $automations = [];

    public function register()
    {
        $this->app->singleton('automations', function () {
            return new AutomationDispatcher();
        });

        $this->booting(function () {
            $automations = $this->getAutomations();

            foreach ($automations as $trigger => $handler) {
                Automation::add($trigger, $handler);
            }
        });
    }

    public function getAutomations(): array
    {
        return $this->automations;
    }

    public function boot()
    {
        //
    }
}
