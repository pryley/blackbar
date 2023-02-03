<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Application;

class Console implements Module
{
    public const ERROR_CODES = [
        E_ERROR => 'error', // 1
        E_WARNING => 'warning', // 2
        E_NOTICE => 'notice', // 8
        E_STRICT => 'strict', // 2048
        E_DEPRECATED => 'deprecated', // 8192
    ];

    public const MAPPED_ERROR_CODES = [
        'debug' => 0,
        'info' => E_NOTICE,
        'deprecated' => E_DEPRECATED, // 8192
        'error' => E_ERROR, // 1
        'notice' => E_NOTICE, // 8
        'strict' => E_STRICT, // 2048
        'warning' => E_WARNING, // 2
        'critical' => E_ERROR, // 1
        'alert' => E_ERROR, // 1
        'emergency' => E_ERROR, // 1
    ];

    /**
     * @var Application
     */
    protected $app;
    /**
     * @var array
     */
    protected $entries;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->entries = [];
    }

    public function entries(): array
    {
        $entries = [];
        foreach ($this->entries as $entry) {
            $entry['name'] = ucfirst($entry['errname']);
            if ($entry['count'] > 1) {
                $entry['name'] = sprintf('%s (%s)', $entry['name'], $entry['count']);
            }
            $entries[] = $entry;
        }
        return $entries;
    }

    public function hasEntries(): bool
    {
        return !empty($this->entries);
    }

    public function id(): string
    {
        return 'glbb-console';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function label(): string
    {
        $class = '';
        $entryCount = count($this->entries);
        $errorCount = 0;
        $label = __('Console', 'blackbar');
        foreach ($this->entries as $entry) {
            if (in_array($entry['errno'], [E_WARNING])) {
                $class = 'glbb-warning';
            }
            if (in_array($entry['errno'], [E_ERROR])) {
                ++$errorCount;
            }
        }
        if ($errorCount > 0) {
            $class = 'glbb-error';
            $label = sprintf('%s (%d, %d!)', $label, $entryCount, $errorCount);
        } elseif ($entryCount > 0) {
            $label = sprintf('%s (%d)', $label, $entryCount);
        }
        return sprintf('<span class="%s">%s</span>', $class, $label);
    }

    public function render(): void
    {
        $this->app->render('panels/console', ['console' => $this]);
    }

    public function store(string $message, string $errno = '', string $location = ''): void
    {
        if (is_numeric($errno)) {
            // entry likely stored by set_error_handler()
            $errname = 'Unknown';
            if (array_key_exists((int) $errno, static::ERROR_CODES)) {
                $errname = static::ERROR_CODES[$errno];
            }
        } else {
            // entry likely stored by filter hook
            $errname = 'Debug';
            if (array_key_exists($errno, static::MAPPED_ERROR_CODES)) {
                $errname = $errno;
                $errno = static::MAPPED_ERROR_CODES[$errno];
            }
        }
        $errname = strtolower($errname);
        $hash = md5($errno.$errname.$message.$location);
        if (array_key_exists($hash, $this->entries)) {
            ++$this->entries[$hash]['count'];
        } else {
            $this->entries[$hash] = [
                'count' => 0,
                'errname' => $errname,
                'errno' => (int) $errno,
                'message' => $this->normalizeMessage($message, $location),
            ];
        };
    }

    protected function normalizeMessage($message, string $location): string
    {
        if ($message instanceof \DateTime) {
            $message = $message->format('Y-m-d H:i:s');
        } elseif (is_object($message) || is_array($message)) {
            $message = print_r(json_decode(json_encode($message)), true);
        } else {
            $message = esc_html(trim((string) $message));
        }
        $location = trim($location);
        if (!empty($location)) {
            $location = sprintf('[%s] ', $location);
        }
        return $location.$message;
    }
}
