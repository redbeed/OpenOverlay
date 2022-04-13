<?php

if (!function_exists('automation')) {
    /**
     * Trigger a automation
     *
     * @param string|object $trigger
     * @return array|string|null
     */
    function automation(...$args)
    {
        return app('automations')->trigger(...$args);
    }
}
