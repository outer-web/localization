<?php

use Outerweb\Localization\Services\Localization;

if (! function_exists('localization')) {
    function localization(): Localization
    {
        return app(Localization::class);
    }
}

if (! function_exists('localizedRoute')) {
    function localizedRoute(string $name, mixed $parameters = [], bool $absolute = true, ?string $locale = null): string
    {
        return localization()->localizedRoute($name, $parameters, $absolute, $locale);
    }
}
