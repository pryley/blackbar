<?php

namespace GeminiLabs\Blackbar;

use DateTime;

class Console
{
    const ERROR_CODES = [
        E_ERROR => 'Error', // 1
        E_WARNING => 'Warning', // 2
        E_NOTICE => 'Notice', // 8
        E_STRICT => 'Strict', // 2048
        E_DEPRECATED => 'Deprecated', // 8192
    ];

    const MAPPED_ERROR_CODES = [
        'debug' => 0,
        'info' => 0,
        'notice' => 0,
        'warning' => E_NOTICE, // 8
        'error' => E_WARNING, // 2
        'critical' => E_WARNING, // 2
        'alert' => E_WARNING, // 2
        'emergency' => E_WARNING, // 2
    ];

    public $entries = [];

    /**
     * @param int|string $errno
     * @return static
     */
    public function store(string $message, $errno = 0, string $location = '')
    {
        $errname = 'Debug';
        if (array_key_exists($errno, static::MAPPED_ERROR_CODES)) {
            $errname = ucfirst($errno);
            $errno = static::MAPPED_ERROR_CODES[$errno];
        } elseif (array_key_exists($errno, static::ERROR_CODES)) {
            $errname = static::ERROR_CODES[$errno];
        }
        $this->entries[] = [
            'errno' => $errno,
            'message' => $location.$this->normalizeValue($message),
            'name' => sprintf('<span class="glbb-info glbb-%s">%s</span>', strtolower($errname), $errname),
        ];
        return $this;
    }

    /**
     * @param mixed $value
     */
    protected function normalizeValue($value): string
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (is_object($value) || is_array($value)) {
            $value = print_r(json_decode(json_encode($value)), true);
        }
        return esc_html((string) $value);
    }
}
