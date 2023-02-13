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
        wp_enqueue_script(Application::ID, $this->app->url('assets/main.js'), [], '4.0.0');
        wp_enqueue_style(Application::ID, $this->app->url('assets/main.css'), ['dashicons'], '4.0.0');
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
    public function initConsole(): void
    {
        if (Application::CONSOLE_HOOK !== func_get_arg(0)) {
            return;
        }
        $args = array_pad(func_get_args(), 4, '');
        $args = array_combine(['hook', 'message', 'errno', 'location'], $args);
        $args = array_map('sanitize_textarea_field', $args);
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
    public function initHooks(): void
    {
        $this->app->hooks->startTimer();
    }

    /**
     * @action all
     */
    public function initProfiler(): void
    {
        $hook = func_get_arg(0);
        if (str_starts_with($hook, 'blackbar/profiler/')) {
            $property = str_replace('blackbar/profiler/', '', $hook);
            $this->app->profiler->set($property);
        } elseif ('timer:start' === $hook) {
            $name = func_num_args() > 1 ? func_get_arg(1) : 'Timer';
            $this->app->profiler->start($name);
        } elseif ('timer:stop' === $hook) {
            $this->app->profiler->stop();
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
        do_action('blackbar/profiler/stop'); // stop profiler
        $this->app->render('debug-bar', [
            'modules' => [ // order is intentional
                $this->app->console,
                $this->app->profiler,
                $this->app->queries,
                $this->app->hooks,
                $this->app->templates,
                $this->app->globals,
            ],
        ]);
    }
}
