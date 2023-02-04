<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Application;

class Globals implements Module
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var array
     */
    protected $entries;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->entries = [];
    }

    public function entries(): array
    {
        if (!empty($this->entries)) {
            return $this->entries;
        }
        if (is_admin() && $screen = get_current_screen()) {
            $reflection = new \ReflectionClass($screen);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            $values = [];
            foreach ($properties as $property) {
                $values[$property->getName()] = $property->getValue($screen);
            }
            $this->entries[] = [
                'name' => 'WP_Screen',
                'value' => var_export($values, true),
            ];
        }
        $this->entries[] = [
            'name' => '$_GET',
            'value' => var_export($_GET, true),
        ];
        $this->entries[] = [
            'name' => '$_SERVER',
            'value' => var_export($_SERVER, true),
        ];
        $this->entries[] = [
            'name' => '$_COOKIE',
            'value' => var_export($_COOKIE, true),
        ];
        $this->entries[] = [
            'name' => '$_SESSION',
            'value' => var_export(isset($_SESSION) ? $_SESSION : [], true),
        ];
        return $this->entries;
    }

    public function hasEntries(): bool
    {
        return !empty($this->entries());
    }

    public function id(): string
    {
        return 'glbb-globals';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function label(): string
    {
        return __('Globals', 'blackbar');
    }

    public function render(): void
    {
        $this->app->render('panels/globals', ['globals' => $this]);
    }
}
