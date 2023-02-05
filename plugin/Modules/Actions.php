<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Application;

class Actions implements Module
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
    protected $hooks;
    /**
     * @var int
     */
    protected $totalActions;
    /**
     * @var float
     */
    protected $totalTime;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->entries = [];
        $this->hooks = [];
        $this->totalActions = 0;
        $this->totalTime = (float) 0;
    }

    public function callbacksForHook(string $action): void
    {
        global $wp_filter;
        if (!array_key_exists($action, $this->hooks)) {
            return;
        }
        $this->hooks[$action]['callbacks_count'] = 0;
        foreach ($wp_filter[$action] as $priority => $callbacks) {
            if (!array_key_exists($priority, $this->hooks[$action]['callbacks'])) {
                $this->hooks[$action]['callbacks'][$priority] = [];
            }
            foreach ($callbacks as $callback) {
                if (is_array($callback['function']) && 2 === count($callback['function'])) {
                    list($object, $method) = $callback['function'];
                    if (is_object($object)) {
                        $object = get_class($object);
                        // $reflection = new \ReflectionClass($object);
                        // if (str_starts_with($reflection->getNamespaceName(), 'GeminiLabs\BlackBar')) {
                        //     continue; // skip blackbar callbacks
                        // }
                    }
                    $this->hooks[$action]['callbacks'][$priority][] = sprintf('%s::%s', $object, $method);
                } elseif (is_object($callback['function'])) {
                    $this->hooks[$action]['callbacks'][$priority][] = get_class($callback['function']);
                } else {
                    $this->hooks[$action]['callbacks'][$priority][] = $callback['function'];
                }
                ++$this->hooks[$action]['callbacks_count'];
            }
        }
    }

    public function entries(): array
    {
        if (class_exists('Debug_Bar_Slow_Actions')) {
            return [];
        }
        if (!empty($this->entries) || empty($this->hooks)) {
            return $this->entries;
        }
        foreach ($this->hooks as $action => $data) {
            $total = $this->totalTimeForHook($data);
            $this->hooks[$action]['total'] = $total;
            $this->totalTime += $total;
            $this->totalActions += $data['count'];
            $this->callbacksForHook($action);
        }
        uasort($this->hooks, [$this, 'sortByTime']);
        $this->entries = array_slice($this->hooks, 0, 50); // return the 50 slowest actions
        return $this->entries;
    }

    public function hasEntries(): bool
    {
        return !empty($this->entries());
    }

    public function id(): string
    {
        return 'glbb-actions';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function label(): string
    {
        $label = __('Hooks', 'blackbar');
        if (class_exists('Debug_Bar_Slow_Actions')) {
            return $label;
        }
        $this->entries(); // calculates the totalTime
        if ($this->totalTime > 0) {
            $label = sprintf('%s (<span class="glbb-actions-time">%.2f</span> %s)', $label, $this->totalTime, __('ms', 'blackbar'));
        }
        return $label;
    }

    public function render(): void
    {
        $this->app->render('panels/actions', ['actions' => $this]);
    }

    public function startTimer(): void
    {
        if (!isset($this->hooks[current_filter()])) {
            $this->hooks[current_filter()] = [
                'callbacks' => [],
                'count' => 0,
                'stack' => [],
                'time' => [],
            ];
            add_action(current_filter(), [$this, 'stopTimer'], 9999); // @phpstan-ignore-line
        }
        ++$this->hooks[current_filter()]['count'];
        array_push($this->hooks[current_filter()]['stack'], ['start' => microtime(true)]);
    }

    /**
     * @param mixed $filteredValue
     * @return mixed
     */
    public function stopTimer($filteredValue = null)
    {
        $time = array_pop($this->hooks[current_filter()]['stack']);
        $time['stop'] = microtime(true);
        array_push($this->hooks[current_filter()]['time'], $time);
        return $filteredValue; // In case this was a filter.
    }

    public function totalTimeForHook(array $data): float
    {
        $total = 0;
        foreach ($data['time'] as $time) {
            $total += ($time['stop'] - $time['start']) * 1000;
        }
        return (float) $total;
    }

    protected function sortByTime(array $a, array $b): int
    {
        if ($a['total'] !== $b['total']) {
            return ($a['total'] > $b['total']) ? -1 : 1;
        }
        return 0;
    }
}
