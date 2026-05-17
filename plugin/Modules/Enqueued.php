<?php

namespace GeminiLabs\BlackBar\Modules;

class Enqueued extends Module
{
    public function entries(): array
    {
        if (!empty($this->entries)) {
            return $this->entries;
        }
        $scriptsHeader = [];
        $scriptsFooter = [];
        $styles = [];
        $scriptsInternal = [];
        $stylesInternal = [];
        foreach (wp_scripts()->done as $handle) {
            $src = wp_scripts()->registered[$handle]->src ?? false;
            if (!is_string($src)) {
                continue;
            }
            if (str_contains($src, '/wp-admin/') || str_contains($src, '/wp-includes/')) {
                $scriptsInternal[$handle] = $src;
                continue;
            }
            if (!in_array($handle, wp_scripts()->in_footer)) {
                $scriptsHeader[$handle] = $src;
                continue;
            }
            $scriptsFooter[$handle] = $src;
        }
        foreach (wp_styles()->done as $handle) {
            $src = wp_styles()->registered[$handle]->src ?? false;
            if (!is_string($src)) {
                continue;
            }
            if (str_contains($src, '/wp-admin/') || str_contains($src, '/wp-includes/')) {
                $stylesInternal[$handle] = $src;
                continue;
            }
            $styles[$handle] = $src;
        }
        ksort($scriptsHeader);
        ksort($scriptsFooter);
        ksort($styles);
        ksort($scriptsInternal);
        ksort($stylesInternal);
        $enqueued = array_filter([
            'CSS' => $styles,
            'JS (Header)' => $scriptsHeader,
            'JS (Footer)' => $scriptsFooter,
            'WordPress CSS' => $stylesInternal,
            'WordPress JS' => $scriptsInternal,
        ]);
        foreach ($enqueued as $key => $values) {
            $this->entries[] = [
                'name' => $key,
                'value' => var_export($values, true),
            ];
        }
        return $this->entries;
    }

    public function icon(): string
    {
        return 'dashicons-admin-links';
    }

    public function hasEntries(): bool
    {
        return !empty($this->entries());
    }

    public function label(): string
    {
        return __('Enqueued', 'blackbar');
    }
}
