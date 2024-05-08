<?php

namespace GeminiLabs\BlackBar\Modules;

class Hooks extends Module
{
    /**
     * @var array
     */
    protected $hooks = [];
    /**
     * @var int
     */
    protected $totalHooks = 0;
    /**
     * Total elapsed time in nanoseconds.
     * @var int
     */
    protected $totalTime = 0;

    public function entries(): array
    {
        if (!$this->hasEntries()) {
            return [];
        }
        if (!empty($this->hooks)) {
            return $this->hooks;
        }
        array_walk($this->entries, function (&$data) {
            $total = $this->totalTimeForHook($data);
            $perCall = (int) round($total / $data['count']);
            $data['per_call'] = $this->formatTime($perCall);
            $data['total'] = $total;
            $data['total_formatted'] = $this->formatTime($total);
        });
        $entries = $this->entries;
        $executionOrder = array_keys($entries);
        uasort($entries, [$this, 'sortByTime']);
        $this->hooks = array_slice($entries, 0, 50); // Keep the 50 slowest hooks
        $this->totalHooks = array_sum(wp_list_pluck($this->entries, 'count'));
        $this->totalTime = array_sum(wp_list_pluck($this->entries, 'total'));
        $order = array_intersect($executionOrder, array_keys($this->hooks));
        foreach ($order as $index => $hook) {
            $this->hooks[$hook]['index'] = $index;
        }
        return $this->hooks;
    }

    public function highlighted(): array
    {
        return [
            'admin_bar_init',
            'admin_bar_menu',
            'admin_enqueue_scripts',
            'admin_footer',
            'admin_head',
            'admin_init',
            'admin_menu',
            'admin_menu',
            'admin_notices',
            'admin_print_footer_scripts',
            'admin_print_scripts',
            'admin_print_styles',
            'after_setup_theme',
            'all_admin_notices',
            'current_screen',
            'get_header',
            'init',
            'load_textdomain',
            'muplugins_loaded',
            'plugin_loaded',
            'plugins_loaded',
            'pre_get_posts',
            'setup_theme',
            'wp',
            'wp_default_scripts',
            'wp_default_styles',
            'wp_enqueue_scripts',
            'wp_footer',
            'wp_head',
            'wp_loaded',
            'wp_print_footer_scripts',
            'wp_print_scripts',
            'wp_print_styles',
            'wp_print_scripts',
        ];
    }

    public function info(): string
    {
        $this->entries(); // calculate the totalTime
        return $this->formatTime($this->totalTime);
    }

    public function label(): string
    {
        return __('Hooks', 'blackbar');
    }

    public function startTimer(): void
    {
        if (class_exists('Debug_Bar_Slow_Actions')) {
            return;
        }
        $hook = current_filter();
        if (!isset($this->entries[$hook])) {
            $callbacks = $this->callbacksForHook($hook);
            if (empty($callbacks)) {
                return; // We skipped Blackbar callbacks
            }
            $this->entries[$hook] = [
                'callbacks' => $callbacks,
                'callbacks_count' => count(array_merge(...$callbacks)),
                'count' => 0,
                'stack' => [],
                'time' => [],
            ];
            add_action($hook, [$this, 'stopTimer'], 9999); // @phpstan-ignore-line
        }
        ++$this->entries[$hook]['count'];
        array_push($this->entries[$hook]['stack'], ['start' => (int) hrtime(true)]);
    }

    /**
     * @param mixed $filteredValue
     * @return mixed
     */
    public function stopTimer($filteredValue = null)
    {
        $time = array_pop($this->entries[current_filter()]['stack']);
        $time['stop'] = (int) hrtime(true);
        array_push($this->entries[current_filter()]['time'], $time);
        return $filteredValue; // In case this was a filter.
    }

    /**
     * @param mixed $function
     */
    protected function callbackFunction($function): string
    {
        if (is_array($function)) {
            list($object, $method) = $function;
            if (is_object($object)) {
                $object = get_class($object);
            }
            if (str_starts_with($object, 'GeminiLabs\BlackBar')) {
                return ''; // skip Blackbar callbacks
            }
            return rtrim(sprintf('%s::%s', $object, $method), ':');
        }
        if (is_object($function)) {
            return get_class($function);
        }
        return (string) $function;
    }

    protected function callbacksForHook(string $hook): array
    {
        global $wp_filter;
        $data = $wp_filter[$hook] ?? [];
        $results = [];
        foreach ($data as $priority => $callbacks) {
            $results[$priority] = $results[$priority] ?? [];
            foreach ($callbacks as $callback) {
                $function = $this->callbackFunction($callback['function']);
                if (!empty($function)) {
                    $results[$priority][] = $function;
                }
            }
        }
        return $results;
    }

    protected function sortByTime(array $a, array $b): int
    {
        if ($a['total'] !== $b['total']) {
            return ($a['total'] > $b['total']) ? -1 : 1;
        }
        return 0;
    }

    /**
     * Total elapsed time in nanoseconds.
     */
    protected function totalTimeForHook(array $data): int
    {
        $total = 0;
        foreach ($data['time'] as $time) {
            $total += ($time['stop'] - $time['start']);
        }
        return $total;
    }
}
