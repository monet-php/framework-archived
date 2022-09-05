<?php

namespace Monet\Framework\Theme\Repository;

use Monet\Framework\Theme\Theme;

interface ThemeRepositoryInterface
{
    public function all(): array;

    public function enabled(): ?Theme;

    public function disabled(): array;

    public function enable(string|Theme $theme, ?string &$reason = null): bool;

    public function disable(): void;

    public function find(string $name): ?Theme;

    public function findOrFail(string $name): Theme;

    public function validate(string|Theme $theme, ?string &$reason = null): bool;

    public function boot(): void;

    public function delete(string|Theme $theme, ?string &$reason = null): bool;

    public function install(string $path, ?string &$reason = null): bool;
}
