<?php

namespace GeminiLabs\BlackBar\Modules;

class Queries extends Module
{
    public function classes(): string
    {
        $entries = $this->entries();
        $isError = array_filter(wp_list_pluck($entries, 'is_error'));
        if (!empty($isError)) {
            return sprintf('%s glbb-error', $this->id());
        }
        $isWarning = array_filter(wp_list_pluck($entries, 'is_warning'));
        if (!empty($isWarning)) {
            return sprintf('%s glbb-warning', $this->id());
        }
        return $this->id();
    }

    public function entries(): array
    {
        if (!empty($this->entries)) {
            return $this->entries;
        }
        global $wpdb;
        $entries = [];
        $index = 0;
        $search = [
            'FROM', 'GROUP BY', 'INNER JOIN', 'LEFT JOIN', 'LIMIT',
            'ON DUPLICATE KEY UPDATE', 'ORDER BY', 'OFFSET', ' SET', 'WHERE',
        ];
        $replace = array_map(function ($value) {
            return PHP_EOL.$value;
        }, $search);
        $errorThreshold = (int) apply_filters('blackbar/queries/ms_error', 1000); // 1s
        $warningThreshold = (int) apply_filters('blackbar/queries/ms_warning', 50); // 50ms
        foreach ($wpdb->queries as $query) {
            $sql = preg_replace('/\s\s+/', ' ', trim($query[0]));
            $sql = str_replace(PHP_EOL, ' ', $sql);
            $sql = str_replace(['( ', ' )', ' ,'], ['(', ')', ','], $sql);
            $sql = str_replace($search, $replace, $sql);
            $parts = explode(PHP_EOL, $sql);
            $sql = array_reduce($parts, function ($carry, $part) {
                if (str_starts_with($part, 'SELECT') && strlen($part) > 100) {
                    $part = preg_replace('/\s*(,)\s*/', ','.PHP_EOL.'  ', $part);
                }
                if (str_starts_with($part, 'WHERE')) {
                    $part = str_replace('AND', PHP_EOL.'  AND', $part);
                }
                return $carry.$part.PHP_EOL;
            });
            $trace = explode(', ', $query[2]);
            $nanoseconds = (int) round($query[1] * 1e9);
            $entries[] = [
                'index' => $index++,
                'is_error' => round($nanoseconds / 1e6, 2) > $errorThreshold,
                'is_warning' => round($nanoseconds / 1e6, 2) > $warningThreshold,
                'sql' => $sql,
                'time' => $nanoseconds,
                'time_formatted' => $this->formatTime($nanoseconds),
                'trace' => array_reverse($trace, true),
            ];
        }
        uasort($entries, [$this, 'sortByTime']);
        $this->entries = $entries;
        return $entries;
    }

    public function hasEntries(): bool
    {
        global $wpdb;
        return !empty($wpdb->queries);
    }

    public function icon(): string
    {
        return 'dashicons-database';
    }

    public function info(): string
    {
        if (!defined('SAVEQUERIES') || !SAVEQUERIES) {
            return '';
        }
        global $wpdb;
        $seconds = (float) array_sum(wp_list_pluck($wpdb->queries, 1));
        $nanoseconds = (int) round($seconds * 1e9);
        return $this->formatTime($nanoseconds);
    }

    public function label(): string
    {
        return __('SQL', 'blackbar');
    }

    protected function sortByTime(array $a, array $b): int
    {
        if ($a['time'] !== $b['time']) {
            return ($a['time'] > $b['time']) ? -1 : 1;
        }
        return 0;
    }
}
