<?php

namespace GeminiLabs\BlackBar;

class Profiler
{
    /**
     * This is the time that WordPress takes to execute the profiler hook.
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

    public function __construct()
    {
        $this->noise = (float) 0;
        $this->start = (float) 0;
        $this->stop = (float) 0;
        $this->timers = [];
    }

    public function getMeasure(): array
    {
        return $this->timers;
    }

    public function getMemoryString(array $timer): string
    {
        return sprintf('%s kB', round($this->normalize($timer)['memory'] / 1000));
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
        $time = number_format(round(($timer['time'] - $start) * 1000, 4), 4);
        return sprintf('%s ms', $time);
    }

    public function getTotalTime(): float
    {
        $totalNoise = (count($this->timers) - 1) * $this->noise;
        return $this->stop - $this->start - $totalNoise;
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
