<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Dump;

class Console extends Module
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

    public function classes(): string
    {
        $errno = array_unique(wp_list_pluck($this->entries, 'errno'));
        if (in_array(E_ERROR, $errno)) {
            return sprintf('%s glbb-error', $this->id());
        }
        if (in_array(E_WARNING, $errno)) {
            return sprintf('%s glbb-warning', $this->id());
        }
        return $this->id();
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

    public function info(): string
    {
        $counts = array_count_values(wp_list_pluck($this->entries, 'errno'));
        $entryCount = count($this->entries);
        if (!empty($counts[E_ERROR])) {
            return sprintf('%d, %d!', $entryCount, $counts[E_ERROR]);
        }
        if ($entryCount > 0) {
            return (string) $entryCount;
        }
        return '';
    }

    public function label(): string
    {
        return __('Console', 'blackbar');
    }

    public function store(string $message, string $errno = '', string $location = ''): void
    {
        if (is_numeric($errno)) { // entry likely stored by set_error_handler()
            $errname = 'Unknown';
            if (array_key_exists((int) $errno, static::ERROR_CODES)) {
                $errname = static::ERROR_CODES[$errno];
            }
        } else { // entry likely stored by filter hook
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
        }
    }

    protected function normalizeMessage($message, string $location): string
    {
        if ($message instanceof \DateTime) {
            $message = $message->format('Y-m-d H:i:s');
        } elseif (is_object($message) || is_array($message)) {
            $message = (new Dump())->dump($message);
        } else {
            $message = esc_html(trim((string) $message));
        }
        $location = trim($location);
        if (!empty($location)) {
            $location = str_replace([WP_CONTENT_DIR, ABSPATH], '', $location);
            $location = sprintf('[%s]', $location);
        }
        return trim(sprintf('%s %s', $location, $message));
    }
}
