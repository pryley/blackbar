<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Application;

abstract class Module
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var array
     */
    protected $entries;
    /**
     * @var array
     */
    public $highlighted;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->entries = [];
        $this->highlighted = $this->highlighted();
    }

    public function classes(): string
    {
        return $this->id();
    }

    abstract public function entries(): array;

    public function hasEntries(): bool
    {
        return !empty($this->entries);
    }

    public function highlighted(): array
    {
        return [];
    }

    public function id(): string
    {
        return sprintf('glbb-%s', $this->slug());
    }

    public function info(): string
    {
        return '';
    }

    public function isVisible(): bool
    {
        return true;
    }

    abstract public function label(): string;

    public function render(): void
    {
        $this->app->render('panels/'.$this->slug(), ['module' => $this]);
    }

    public function slug(): string
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    protected function formatTime(int $nanoseconds): string
    {
        if ($nanoseconds >= 1e9) {
            return sprintf('%s s', $this->toDecimal(round($nanoseconds / 1e9, 2)));
        }
        if ($nanoseconds >= 1e6) {
            return sprintf('%s ms', $this->toDecimal(round($nanoseconds / 1e6, 2)));
        }
        if ($nanoseconds >= 1e3) {
            return sprintf('%s µs', round($nanoseconds / 1e3));
        }
        return sprintf('%s ns', $nanoseconds);
    }

    protected function toDecimal(float $number): string
    {
        $number = (string) $number;
        if (false !== strpos($number, '.')) {
            $number = rtrim(rtrim($number, '0'), '.');
        }
        return $number;
    }
}
