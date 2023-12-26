<?php

namespace Outerweb\Localization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = localization()->getLocaleFromRequest($request);

        localization()->setCurrentLocale($locale);

        return $next($request);
    }
}
