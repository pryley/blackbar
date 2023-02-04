<?php

namespace GeminiLabs\BlackBar;

class Controller
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
     * @action all
     */
    public function initActions(): void
    {
        if (!class_exists('Debug_Bar_Slow_Actions')) {
            $this->app->actions->startTimer();
        }
    }

    /**
     * @action all
     */
    public function initConsole(): void
    {
        if (Application::CONSOLE_HOOK !== func_get_arg(0)) {
            return;
        }
        $args = array_pad(func_get_args(), 4, '');
        $args = array_combine(['hook', 'message', 'errno', 'location'], $args);
        $args = array_map('sanitize_text_field', $args);
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        $entry = array_pop($backtrace); // get the fourth backtrace entry
        if (empty(trim($args['location'])) && array_key_exists('file', $entry)) {
            $path = explode(ABSPATH, $entry['file']);
            $args['location'] = sprintf('%s:%s', array_pop($path), $entry['line']);
        }
        $this->app->console->store($args['message'], $args['errno'], $args['location']);
    }

    /**
     * @action all
     */
    public function initProfiler(): void
    {
        if (Application::PROFILER_HOOK === func_get_arg(0)) {
            $this->app->profiler->trace(func_get_arg(1));
        }
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
            'modules' => [ // order is intentional
                $this->app->console,
                $this->app->profiler,
                $this->app->queries,
                $this->app->actions,
                $this->app->templates,
                $this->app->globals,
            ],
        ]);
    }
}
