<?php

namespace GeminiLabs\BlackBar;

use GeminiLabs\BlackBar\Modules\Actions;
use GeminiLabs\BlackBar\Modules\Globals;
use GeminiLabs\BlackBar\Modules\Profiler;
use GeminiLabs\BlackBar\Modules\Queries;
use GeminiLabs\BlackBar\Modules\Templates;
use GeminiLabs\BlackBar\Modules\Console;

final class Application
{
    const CONSOLE_HOOK = 'console';
    const ID = 'blackbar';
    const PROFILER_HOOK = 'profile';

    public $actions;
    public $console;
    public $file;
    public $globals;
    public $profiler;
    public $queries;
    public $templates;

    protected static $instance;

    public function __construct()
    {
        $file = wp_normalize_path((new \ReflectionClass($this))->getFileName());
        $this->actions = new Actions($this);
        $this->console = new Console($this);
        $this->file = str_replace('plugin/Application', static::ID, $file);
        $this->globals = new Globals($this);
        $this->profiler = new Profiler($this);
        $this->queries = new Queries($this);
        $this->templates = new Templates($this);
    }

    public function errorHandler(int $errno, string $message, string $file, int $line): bool
    {
        $path = explode(ABSPATH, $file);
        $location = sprintf('%s:%s', array_pop($path), $line);
        $this->console->store($message, (string) $errno, $location);
        return true;
    }

    public function init(): void
    {
        $controller = new Controller($this);
        add_action('all', [$controller, 'initActions']);
        add_action('all', [$controller, 'initConsole']);
        add_action('all', [$controller, 'initProfiler']);
        add_action('plugins_loaded', [$controller, 'registerLanguages']);
        add_action('init', function () use ($controller) {
            if (!apply_filters('blackbar/enabled', current_user_can('administrator'))) {
                return;
            }
            add_action('admin_enqueue_scripts', [$controller, 'enqueueAssets']);
            add_action('wp_enqueue_scripts', [$controller, 'enqueueAssets']);
            add_action('admin_footer', [$controller, 'renderBar'], 99999);
            add_action('wp_footer', [$controller, 'renderBar'], 99999);
            add_filter('admin_body_class', [$controller, 'filterBodyClasses']);
        });
        apply_filters('debug', 'Profiler Started');
        apply_filters('debug', 'blackbar/profiler/noise');
        set_error_handler([$this, 'errorHandler'], E_ALL | E_STRICT);
    }

    /**
     * @return static
     */
    public static function load()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function path(string $file = '', bool $realpath = true): string
    {
        $path = $realpath
            ? plugin_dir_path($this->file)
            : trailingslashit(WP_PLUGIN_DIR).basename(dirname($this->file));
        return trailingslashit($path).ltrim(trim($file), '/');
    }

    public function render(string $view, array $data = []): void
    {
        $file = $this->path(sprintf('views/%s.php', str_replace('.php', '', $view)));
        if (!file_exists($file)) {
            return;
        }
        extract($data);
        include $file;
    }

    public function url(string $path = ''): string
    {
        return esc_url(plugin_dir_url($this->file).ltrim(trim($path), '/'));
    }
}
