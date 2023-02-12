<?php

namespace GeminiLabs\BlackBar\Modules;

class Profiler extends Module
{
    /**
     * @var int
     */
    protected $memory_start = 0;
    /**
     * @var int
     */
    protected $memory_stop = 0;
    /**
     * The profiler noise to remove from the timer (in nanoseconds).
     * @var int
     */
    protected $noise = 0;
    /**
     * The hrtime the profiler started measuring (in nanoseconds).
     * @var int
     */
    protected $start = 0;
    /**
     * The hrtime the profiler stopped measuring (in nanoseconds).
     * @var int
     */
    protected $stop = 0;
    /**
     * @var array
     */
    protected $timer = [];

    public function entries(): array
    {
        $entries = [];
        foreach ($this->entries as $entry) {
            $entry['time'] = $this->formatTime($entry['time']);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function isVisible(): bool
    {
        return $this->hasEntries();
    }

    public function label(): string
    {
        return __('Profiler', 'blackbar');
    }

    public function set(string $property): void
    {
        if ('noise' === $property) {
            $this->noise = (int) hrtime(true) - $this->start;
        } elseif ('start' === $property) {
            $this->start = (int) hrtime(true);
            $this->memory_start = memory_get_peak_usage();
        } elseif ('stop' === $property) {
            $this->stop = (int) hrtime(true);
            $this->memory_stop = memory_get_peak_usage();
        }
    }

    public function start(string $name): void
    {
        $this->timer = [
            'memory' => memory_get_peak_usage(),
            'name' => $name,
            'start' => (int) hrtime(true),
            'stop' => 0,
            'time' => 0,
        ];
    }

    public function stop(): void
    {
        if (!empty($this->timer)) {
            $nanoseconds = (int) hrtime(true);
            $this->timer['memory'] = max(0, memory_get_peak_usage() - $this->timer['memory']);
            $this->timer['stop'] = $nanoseconds;
            $this->timer['time'] = max(0, $nanoseconds - $this->timer['start'] - $this->noise);
            $this->entries[] = $this->timer;
            $this->timer = []; // reset timer
        }
    }
}
