<?php

namespace CodeStencil;

trait LaravelStringHelpers
{
    protected static bool $isLaravelStringHelpersEnabled = true;

    public static function disableLaravelStringHelpers(): void
    {
        static::$isLaravelStringHelpersEnabled = false;
    }

    protected function isLaravelEnvironment(): bool
    {
        return class_exists('Illuminate\Support\Str');
    }

    protected function registerLaravelStringFunctions(): void
    {
        if (!static::$isLaravelStringHelpersEnabled) {
            return;
        }

        if ($this->isLaravelEnvironment()) {
            foreach ($this->getLaravelStringMethods() as $method) {
                $this->function($method, fn(...$args) => $method(...$args));
            }
        }
    }

    protected function getLaravelStringMethods(): array
    {
        // @phpstan-ignore-next-line
        return array_filter(get_class_methods(new \Illuminate\Support\Str()), function($method) {
            return !str_starts_with($method, '__') &&
                !str_starts_with($method, 'when') &&
                !str_starts_with($method, 'is') &&
                !str_ends_with($method, 'Normally') &&
                !str_ends_with($method, 'Using') &&
                !str_ends_with($method, 'UsingSequence') &&
                !in_array($method, ['unless', 'dump', 'dd', 'tap', 'macro', 'mixin', 'flushMacros', 'hasMacro']);
        });
    }
}
