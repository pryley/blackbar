<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Application;

class Profiler implements Module
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var float
     */
    protected $noise;
    /**
     * @var float
     */
    protected $start;
    /**
     * @var float
     */
    protected $stop;
    /**
     * @var array
     */
    protected $timers;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->noise = (float) 0; // This is the time that WordPress takes to execute the all hook.
        $this->start = (float) 0;
        $this->stop = (float) 0;
        $this->timers = [];
    }

    public function entries(): array
    {
        $entries = [];
        foreach ($this->timers as $timer) {
            $entries[] = [
                'memory' => $this->getMemoryString($timer),
                'name' => $this->getNameString($timer),
                'time' => $this->getTimeString($timer),
            ];
        }
        return $entries;
    }

    public function getMeasure(): array
    {
        return $this->timers;
    }

    public function getMemoryString(array $timer): string
    {
        return (string) round($this->normalize($timer)['memory'] / 1000);
    }

    public function getNameString(array $timer): string
    {
        return $this->normalize($timer)['name'];
    }

    public function getTimeString(array $timer): string
    {
        $timer = $this->normalize($timer);
        $index = array_search($timer['name'], array_column($this->timers, 'name'));
        $start = $this->start + ($index * $this->noise);
        return number_format(round(($timer['time'] - $start) * 1000, 4), 4);
    }

    public function getTotalTime(): float
    {
        $totalNoise = (count($this->timers) - 1) * $this->noise;
        return $this->stop - $this->start - $totalNoise;
    }

    public function hasEntries(): bool
    {
        return !empty($this->timers);
    }

    public function id(): string
    {
        return 'glbb-profiler';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function label(): string
    {
        $label = __('Profiler', 'blackbar');
        $time = number_format($this->getTotalTime() * 1000, 0);
        if ($time > 0) {
            $label .= sprintf(' (%s %s)', $time, __('ms', 'blackbar'));
        }
        return $label;
    }

    public function render(): void
    {
        $this->app->render('panels/profiler', ['profiler' => $this]);
    }

    public function trace(string $name): void
    {
        $microtime = microtime(true); // float
        if (!$this->start) {
            $this->start = $microtime;
        }
        if ('blackbar/profiler/noise' === $name) {
            $this->noise = $microtime - $this->start;
            return;
        }
        $this->timers[] = [
            'memory' => memory_get_peak_usage(),
            'name' => $name,
            'time' => $microtime,
        ];
        $this->stop = $microtime;
    }

    protected function normalize(array $timer): array
    {
        return wp_parse_args($timer, [
            'memory' => 0,
            'name' => '',
            'time' => (float) 0,
        ]);
    }
}
