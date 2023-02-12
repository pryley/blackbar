<?php

namespace GeminiLabs\BlackBar\Modules;

class Templates extends Module
{
    public function entries(): array
    {
        if (!empty($this->entries)) {
            return $this->entries;
        }
        if (class_exists('\GeminiLabs\Castor\Facades\Development')
            && class_exists('\GeminiLabs\Castor\Helpers\Development')
            && method_exists('\GeminiLabs\Castor\Helpers\Development', 'templatePaths')) { // @phpstan-ignore-line
            $this->entries = \GeminiLabs\Castor\Facades\Development::templatePaths();
        } else {
            $files = array_values(array_filter(get_included_files(), function ($file) {
                $bool = false !== strpos($file, '/themes/') && false === strpos($file, '/functions.php');
                return (bool) apply_filters('blackbar/templates/file', $bool, $file);
            }));
            $this->entries = array_map(function ($file) {
                return str_replace(trailingslashit(WP_CONTENT_DIR), '', $file);
            }, $files);
        }
        return $this->entries;
    }

    public function hasEntries(): bool
    {
        return !empty($this->entries());
    }

    public function isVisible(): bool
    {
        return !is_admin() && $this->hasEntries();
    }

    public function label(): string
    {
        return __('Templates', 'blackbar');
    }
}
