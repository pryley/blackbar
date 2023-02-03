<?php

namespace GeminiLabs\BlackBar\Modules;

use GeminiLabs\BlackBar\Application;

class Queries implements Module
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function entries(): array
    {
        global $wpdb;
        $entries = [];
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
            $entries[] = [
                'sql' => $sql,
                'time' => $miliseconds,
            ];
        }
        return $entries;
    }

    public function hasEntries(): bool
    {
        global $wpdb;
        return !empty($wpdb->queries);
    }

    public function id(): string
    {
        return 'glbb-queries';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function label(): string
    {
        $label = __('SQL', 'blackbar');
        if (!defined('SAVEQUERIES') || !SAVEQUERIES) {
            return $label;
        }
        global $wpdb;
        $queryTime = 0;
        foreach ($wpdb->queries as $query) {
            $queryTime += $query[1];
        }
        $queryTime = number_format($queryTime * 1000, 2);
        $queriesCount = sprintf('<span class="glbb-queries-count">%s</span>', count($wpdb->queries));
        $queriesTime = sprintf('<span class="glbb-queries-time">%s</span>', $queryTime);
        return $label.sprintf(' (%s %s | %s %s)', $queriesCount, __('queries', 'blackbar'), $queriesTime, __('ms', 'blackbar'));
    }

    public function render(): void
    {
        $this->app->render('panels/queries', ['queries' => $this]);
    }
}
