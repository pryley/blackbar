<?php

namespace GeminiLabs\BlackBar;

class SlowActions
{
    /**
     * @var array
     */
    protected $flow;
    /**
     * This is the time that WordPress takes to execute the all hook.
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
     * @var int
     */
    protected $totalActions;
    /**
     * @var float
     */
    protected $totalTime;

    public function __construct()
    {
        $this->flow = [];
        $this->noise = (float) 0;
        $this->start = microtime(true);
        $this->stop = (float) 0;
        $this->totalActions = 0;
        $this->totalTime = (float) 0;
    }

    public function getTotalTimeForHook(array $data): float
    {
        $total = 0;
        foreach ($data['time'] as $time) {
            $total += ($time['stop'] - $time['start']) * 1000;
        }
        return (float) $total;
    }

    public function addCallbacksForAction(string $action): void
    {
        global $wp_filter;
        if (!array_key_exists($action, $this->flow)) {
            return;
        }
        $this->flow[$action]['callbacks_count'] = 0;
        foreach ($wp_filter[$action] as $priority => $callbacks) {
            if (!array_key_exists($priority, $this->flow[$action]['callbacks'])) {
                $this->flow[$action]['callbacks'][$priority] = [];
            }
            foreach ($callbacks as $callback) {
                if (is_array($callback['function']) && 2 == count($callback['function'])) {
                    list($object, $method) = $callback['function'];
                    if (is_object($object)) {
                        $object = get_class($object);
                    }
                    $this->flow[$action]['callbacks'][$priority][] = sprintf('%s::%s', $object, $method);
                } elseif (is_object($callback['function'])) {
                    $this->flow[$action]['callbacks'][$priority][] = get_class($callback['function']);
                } else {
                    $this->flow[$action]['callbacks'][$priority][] = $callback['function'];
                }
                ++$this->flow[$action]['callbacks_count'];
            }
        }
    }

    public function getMeasure(): array
    {
        foreach ($this->flow as $action => $data) {
            $total = $this->getTotalTimeForHook($data);
            $this->flow[$action]['total'] = $total;
            $this->totalTime += $total;
            $this->totalActions += $data['count'];
            $this->addCallbacksForAction($action);
        }
        uasort($this->flow, [$this, 'sortByTime']);
        return $this->flow;
    }

    public function getTotalActions(): int
    {
        return $this->totalActions;
    }

    public function getTotalTime(): float
    {
        return $this->totalTime;
        // $totalNoise = (count($this->timers) - 1) * $this->noise;
        // return $this->stop - $this->start - $totalNoise;
    }

    public function startTimer(): void
    {
        if (!isset($this->flow[current_filter()])) {
            $this->flow[current_filter()] = [
                'callbacks' => [],
                'count' => 0,
                'stack' => [],
                'time' => [],
            ];
            add_action(current_filter(), [$this, 'stopTimer'], 9000);
        }
        $count = ++$this->flow[current_filter()]['count'];
        array_push($this->flow[current_filter()]['stack'], ['start' => microtime(true)]);
    }

    /**
     * @param mixed $possibleFilter
     * @return mixed
     */
    public function stopTimer($possibleFilter = null)
    {
        $time = array_pop($this->flow[current_filter()]['stack']);
        $time['stop'] = microtime(true);
        array_push($this->flow[current_filter()]['time'], $time);
        return $possibleFilter;
    }

    protected function sortByTime(array $a, array $b): int
    {
        if ($a['total'] == $b['total']) {
            return 0;
        }
        return ($a['total'] > $b['total']) ? -1 : 1;
    }
}
