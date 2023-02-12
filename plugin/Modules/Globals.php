<?php

namespace GeminiLabs\BlackBar\Modules;

class Globals extends Module
{
    public function entries(): array
    {
        if (!empty($this->entries)) {
            return $this->entries;
        }
        $globals = apply_filters('blackbar/globals', [
            'INPUT_COOKIE' => $_COOKIE,
            'INPUT_ENV' => $_ENV,
            'INPUT_GET' => $_GET,
            'INPUT_POST' => $_POST,
            'INPUT_SERVER' => $_SERVER,
            'WP_Screen' => $this->wpscreen(),
        ]);
        $globals = array_filter($globals);
        foreach ($globals as $key => $values) {
            $this->entries[] = [
                'name' => $key,
                'value' => var_export($values, true),
            ];
        }
        return $this->entries;
    }

    public function hasEntries(): bool
    {
        return !empty($this->entries());
    }

    public function label(): string
    {
        return __('Globals', 'blackbar');
    }

    protected function wpscreen(): array
    {
        $values = [];
        if (is_admin() && $screen = get_current_screen()) {
            $reflection = new \ReflectionClass($screen);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                $values[$property->getName()] = $property->getValue($screen);
            }
        }
        return $values;
    }
}
