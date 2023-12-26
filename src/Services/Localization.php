<?php

namespace Outerweb\Localization\Services;

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Localization
{
    public function cookieKey(): string
    {
        return config('localization.cookies.locale');
    }

    public function fallbackLocale(): string
    {
        return config('localization.fallback_locale') ?? config('app.fallback_locale');
    }

    public function getLocaleFromRequest($request): string
    {
        // Get the locale from the first segment of the URL
        $locale = $this->supportedLocales()->get($request->segment(1));

        if ($locale) {
            return $locale;
        }

        // Get the locale from the cookies
        $locale = $this->supportedLocales()->get($request->cookie($this->cookieKey()));

        if ($locale) {
            return $locale;
        }

        // Get the locale from the preferred language header
        $preferredLanguage = $request->getPreferredLanguage();

        // Check for an exact match
        $locale = $this->supportedLocales()->get($preferredLanguage);

        if ($locale) {
            return $locale;
        }

        // $preferredLanguage can be something like en-US, so check for a partial match
        $locale = $this->supportedLocales()->filter(function ($value, $key) use ($preferredLanguage) {
            return str_starts_with($preferredLanguage, $key);
        })->first();

        if ($locale) {
            return $locale;
        }

        // Return the fallback locale
        return $this->fallbackLocale();
    }

    public function listLocalizedRoutesForCurrentRoute(bool $absolute = true): Collection
    {
        return $this->listLocalizedRoutesForRoute(
            request()->route()->getName(),
            request()->route()->originalParameters(),
            $absolute
        );
    }

    public function listLocalizedRoutesForRoute(string $name, array $parameters = [], bool $absolute = true): Collection
    {
        $name = preg_replace('/^[a-z]{2}\./', '', $name);

        return $this->supportedLocales()->map(function ($locale) use ($name, $parameters, $absolute) {
            return $this->localizedRoute($name, $parameters, $absolute, $locale);
        });
    }

    public function localizedRoute(string $name, mixed $parameters = [], bool $absolute = true, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        $name = $locale.'.'.$name;

        return route($name, $parameters, $absolute);
    }

    public function registerRoutes($callback)
    {
        $this->supportedLocales()->each(function ($locale) use ($callback) {
            Route::prefix($locale)
                ->name("{$locale}.")
                ->group($callback);
        });

        collect(Route::getRoutes()->getRoutes())
            ->each(function (RoutingRoute $route) {
                $locale = $this->supportedLocales()->get(Str::before($route->getName(), '.'));

                if (! $locale) {
                    return;
                }

                $route->uri = $this->translateRouteSegments($route->uri, $locale);
            });
    }

    public function setCurrentLocale(string $locale): void
    {
        app()->setLocale($locale);
    }

    public function supportedLocales(): Collection
    {
        $locales = config('localization.supported_locales') ?? config('app.supported_locales');

        if (! is_array($locales)) {
            $locales = [$this->fallbackLocale()];
        }

        return collect(array_combine($locales, $locales));
    }

    public function translationsFileName(): string
    {
        return config('localization.translations_file_name');
    }

    public function translateRouteSegments(string $uri, string $locale): string
    {
        $segments = collect(explode('/', $uri));

        $uri = $segments->map(function ($segment) use ($locale) {
            // If the segment is a route parameter, return as is
            if (str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
                return $segment;
            }

            $key = "{$this->translationsFileName()}.{$segment}";

            if (app('translator')->has($key, $locale)) {
                return app('translator')->get($key, [], $locale);
            }

            return $segment;
        })->implode('/');

        return $uri;
    }
}
