<?php

namespace GeminiLabs\BlackBar;

class Controller
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct()
    {
        $this->app = Application::load();
    }

    /**
     * @action admin_enqueue_scripts
     * @action wp_enqueue_scripts
     */
    public function enqueueAssets(): void
    {
        wp_enqueue_script(Application::ID, $this->app->url('assets/main.js'));
        wp_enqueue_style(Application::ID, $this->app->url('assets/main.css'), ['dashicons']);
        wp_enqueue_style(Application::ID.'-syntax', $this->app->url('assets/syntax.css'));
    }

    /**
     * @param string $classes
     * @action admin_body_class
     */
    public function filterBodyClasses($classes): string
    {
        return trim((string) $classes.' '.Application::ID);
    }

    /**
     * @filter all
     */
    public function initConsole(): void
    {
        if (Application::CONSOLE_HOOK !== func_get_arg(0)) {
            return;
        }
        $args = array_pad(func_get_args(), 4, '');
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        $entry = array_pop($backtrace);
        $message = $args[1];
        $errno = $args[2];
        $location = $args[3];
        if (empty(trim($location)) && array_key_exists('file', $entry)) {
            $path = explode(ABSPATH, $entry['file']);
            $location = sprintf('%s:%s', array_pop($path), $entry['line']);
        }
        $this->app->console->store($message, $errno, '['.$location.'] ');
    }

    /**
     * @filter all
     */
    public function initProfiler(): void
    {
        if (Application::PROFILER_HOOK === func_get_arg(0)) {
            $this->app->profiler->trace(func_get_arg(1));
        }
    }

    /**
     * @filter all
     */
    public function measureSlowActions(): void
    {
        $this->app->actions->startTimer();
    }

    /**
     * @action plugins_loaded
     */
    public function registerLanguages(): void
    {
        load_plugin_textdomain(Application::ID, false,
            plugin_basename($this->app->path()).'/languages/'
        );
    }

    /**
     * @action admin_footer
     * @action wp_footer
     */
    public function renderBar(): void
    {
        apply_filters('debug', 'Profiler Stopped');
        $this->app->render('debug-bar', [
            'actions' => $this->app->actions,
            'actionsLabel' => $this->getSlowActionsLabel(),
            'blackbar' => $this->app,
            'consoleEntries' => $this->getConsoleEntries(),
            'consoleLabel' => $this->getConsoleLabel(),
            'profiler' => $this->app->profiler,
            'profilerLabel' => $this->getProfilerLabel(),
            'queries' => $this->getQueries(),
            'queriesLabel' => $this->getQueriesLabel(),
            'templates' => $this->getTemplates(),
        ]);
    }

    protected function convertToMiliseconds(float $time, int $decimals = 2): string
    {
        return number_format($time * 1000, $decimals);
    }

    protected function getConsoleEntries(): array
    {
        return array_merge($this->getErrors(), $this->app->console->entries);
    }

    protected function getConsoleLabel(): string
    {
        $class = '';
        $entries = $this->getConsoleEntries();
        $entryCount = count($entries);
        $errorCount = 0;
        $label = __('Console', 'blackbar');
        foreach ($entries as $entry) {
            if (in_array($entry['code'], [E_NOTICE, E_STRICT, E_DEPRECATED])) {
                $class = 'glbb-warning';
            }
            if (in_array($entry['code'], [E_WARNING])) {
                ++$errorCount;
            }
        }
        if ($entryCount > 0) {
            $label .= sprintf(' (%d)', $entryCount);
        }
        if ($errorCount > 0) {
            $class = 'glbb-error';
            $label .= sprintf(' (%d, %d!)', $entryCount, $errorCount);
        }
        return sprintf('<span class="%s">%s</span>', $class, $label);
    }

    protected function getErrors(): array
    {
        $errors = [];
        foreach ($this->app->errors as $error) {
            $class = 'glbb-info';
            if (in_array($error['code'], [E_NOTICE, E_STRICT, E_DEPRECATED])) {
                $class = 'glbb-warning';
            }
            if (E_WARNING == $error['code']) {
                $class = 'glbb-error';
            }
            if ($error['count'] > 1) {
                $error['name'] .= ' ('.$error['count'].')';
            }
            $errors[] = [
                'code' => $error['code'],
                'name' => '<span class="'.$class.'">'.$error['name'].'</span>',
                'message' => sprintf(__('%s on line %s in file %s', 'blackbar'),
                    $error['message'],
                    $error['line'],
                    $error['file']
                ),
            ];
        }
        return $errors;
    }

    protected function getIncludedFiles(): array
    {
        $files = array_values(array_filter(get_included_files(), function ($file) {
            $bool = false !== strpos($file, '/themes/') && false === strpos($file, '/functions.php');
            return (bool) apply_filters('blackbar/templates/file', $bool, $file);
        }));
        return array_map(function ($key, $value) {
            $value = str_replace(trailingslashit(WP_CONTENT_DIR), '', $value);
            return sprintf('[%s] => %s', $key, $value);
        }, array_keys($files), $files);
    }

    protected function getProfilerLabel(): string
    {
        $label = __('Profiler', 'blackbar');
        $profilerTime = $this->convertToMiliseconds($this->app->profiler->getTotalTime(), 0);
        if ($profilerTime > 0) {
            $label .= sprintf(' (%s %s)', $profilerTime, __('ms', 'blackbar'));
        }
        return $label;
    }

    protected function getQueries(): array
    {
        global $wpdb;
        $queries = [];
        $search = [
            'AND', 'FROM', 'GROUP BY', 'INNER JOIN', 'LEFT JOIN', 'LIMIT',
            'ON DUPLICATE KEY UPDATE', 'ORDER BY', 'OFFSET', ' SET', 'WHERE',
        ];
        $replace = array_map(function ($value) {
            return PHP_EOL.$value;
        }, $search);
        foreach ($wpdb->queries as $query) {
            $miliseconds = number_format(round($query[1] * 1000, 4), 4);
            $sql = preg_replace('/\s\s+/', ' ', trim($query[0]));
            $sql = str_replace(PHP_EOL, ' ', $sql);
            $sql = str_replace($search, $replace, $sql);
            $queries[] = [
                'ms' => $miliseconds,
                'sql' => $sql,
            ];
        }
        return $queries;
    }

    protected function getQueriesLabel(): string
    {
        $label = __('SQL', 'blackbar');
        if (!SAVEQUERIES) {
            return $label;
        }
        global $wpdb;
        $queryTime = 0;
        foreach ($wpdb->queries as $query) {
            $queryTime += $query[1];
        }
        $queriesCount = sprintf('<span class="glbb-queries-count">%s</span>', count($wpdb->queries));
        $queriesTime = sprintf('<span class="glbb-queries-time">%s</span>', $this->convertToMiliseconds((float) $queryTime));
        return $label.sprintf(' (%s %s | %s %s)', $queriesCount, __('queries', 'blackbar'), $queriesTime, __('ms', 'blackbar'));
    }

    protected function getSlowActionsLabel(): string
    {
        $label = __('Hooks', 'blackbar');
        $totalTime = $this->convertToMiliseconds($this->app->actions->getTotalTime(), 0);
        if ($totalTime > 0) {
            $label .= sprintf(' (%s %s)', $totalTime, __('ms', 'blackbar'));
        }
        return $label;
    }

    protected function getTemplates(): string
    {
        if (is_admin()) {
            return '';
        }
        if (class_exists('\GeminiLabs\Castor\Facades\Development')) {
            ob_start();
            \GeminiLabs\Castor\Facades\Development::printTemplatePaths();
            return ob_get_clean();
        }
        return sprintf('<pre>%s</pre>', implode(PHP_EOL, $this->getIncludedFiles()));
    }
}
