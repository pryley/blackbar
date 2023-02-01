<?php

namespace GeminiLabs\BlackBar;

final class Application
{
    const CONSOLE_HOOK = 'console';
    const ID = 'blackbar';
    const PROFILER_HOOK = 'profile';

    public $actions;
    public $console;
    public $errors = [];
    public $file;
    public $profiler;

    protected static $instance;

    public function __construct()
    {
        $file = wp_normalize_path((new \ReflectionClass($this))->getFileName());
        $this->actions = new SlowActions();
        $this->console = new Console();
        $this->file = str_replace('plugin/Application', static::ID, $file);
        $this->profiler = new Profiler();
    }

    public function errorHandler(int $errno, string $message, string $file, int $line): void
    {
        $errname = array_key_exists($errno, Console::ERROR_CODES)
            ? Console::ERROR_CODES[$errno]
            : 'Unknown';
        $hash = md5($errno.$message.$file.$line);
        if (array_key_exists($hash, $this->errors)) {
            ++$this->errors[$hash]['count'];
        } else {
            $this->errors[$hash] = [
                'count' => 0,
                'errno' => $errno,
                'file' => $file,
                'line' => $line,
                'message' => $message,
                'name' => $errname,
            ];
        }
    }

    public function init(): void
    {
        $controller = new Controller();
        add_filter('all', [$controller, 'initConsole']);
        add_filter('all', [$controller, 'initProfiler']);
        add_filter('all', [$controller, 'measureSlowActions']);
        add_action('plugins_loaded', [$controller, 'registerLanguages']);
        add_action('init', function () use ($controller) {
            if (!apply_filters('blackbar/enabled', current_user_can('administrator'))) {
                return;
            }
            add_action('admin_enqueue_scripts', [$controller, 'enqueueAssets']);
            add_action('wp_enqueue_scripts', [$controller, 'enqueueAssets']);
            add_action('admin_footer', [$controller, 'renderBar']);
            add_action('wp_footer', [$controller, 'renderBar']);
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
