<?php

namespace GeminiLabs\BlackBar\Modules;

interface Module
{
    public function entries(): array;
    public function hasEntries(): bool;
    public function id(): string;
    public function isVisible(): bool;
    public function label(): string;
    public function render(): void;
}
