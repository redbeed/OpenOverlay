<?php

namespace Redbeed\OpenOverlay\Automations\Actions;

use Illuminate\Support\Str;

trait UseVariables
{
    private array $variables = [];

    public function addVariables(array $variables): void
    {
        $this->variables = array_merge_recursive($this->variables, $variables);
    }

    public function getVariables($filterName = ''): array
    {
        if ($filterName) {
            return $this->filterVariables($filterName);
        }

        return $this->variables;
    }

    private function filterVariables(string $filterName): array
    {
        $filtered = [];
        foreach ($this->variables as $name => $value) {
            if (str_contains($name, $filterName)) {
                $filtered[$name] = $value;
            }
        }

        return $filtered;
    }

    protected function replaceInString(string $string): string
    {
        return strtr($string, $this->makeReplacements($string));
    }

    protected function makeReplacements(string $string): array
    {
        $replacements = [];
        foreach ($this->variables as $key => $value) {
            $keyPattern = ':'.$key;

            if (Str::contains($string, $keyPattern)) {
                if ($this->variables[$key] instanceof \Closure) {
                    // If the variable is a closure, we will execute it and replace the key with the result.
                    $this->variables[$key] = $this->variables[$key]();
                }

                $replacements[$keyPattern] = $this->variables[$key];
            }
        }

        return $replacements;
    }
}
