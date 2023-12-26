# Localization

[![Latest Version on Packagist](https://img.shields.io/packagist/v/outerweb/localization.svg?style=flat-square)](https://packagist.org/packages/outerweb/localization)
[![Total Downloads](https://img.shields.io/packagist/dt/outerweb/localization.svg?style=flat-square)](https://packagist.org/packages/outerweb/localization)

This package adds multi-language support to your Laravel application:

- Multiple locales configuration
- Localized routes
- Translatable route segments
- Automatic user locale detection

## Installation

You can install the package via composer:

```bash
composer require outerweb/localization
```

Add the `Outerweb\Localization\Http\Middleware\SetLocale` middleware to the `web` middleware group in `app/Http/Kernel.php`:
This will automatically set the locale for each request by going through the following steps:

1. Check if the locale is set in the URL (e.g. `http://example.com/en`)
2. Check if the locale is set in a cookie
3. Check for a matching locale in the preferred languages of the user's browser
4. Use the fallback locale

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Outerweb\Localization\Http\Middleware\SetLocale::class,
    ],
];
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="localization-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * The cookies that this package will use internally..
     * If your app already uses some other cookie name,
     * you can change it here to make it more uniform.
     */
    'cookies' => [
        'locale' => 'locale',
    ],

    /**
     * If you prefer to define this in config/app.php,
     * leave this as null. It will then fallback to
     * the app.fallback_locale config value.
     */
    'fallback_locale' => null,

    /**
     * If you prefer to define this in config/app.php,
     * leave this as null. It will then fallback to
     * the app.supported_locales config value.
     */
    'supported_locales' => null,

    /**
     * The name of the translations file in the
     * lang directory. (default: routes.php)
     */
    'translations_file_name' => 'routes',
];
```

## Usage

### Defining routes

Define the routes you want to localize like this:

```php
Route::localized(function () {
    Route::get('/', function () {
        return view('welcome');
    })
        ->name('home');
});
```

This will then generate the home route for each locale you have defined in the `supported_locales` config value.

For example, if you have defined `en` and `nl` as supported locales, the following routes will be generated:

- `http://example.com/en` (route name: `en.home`)
- `http://example.com/nl` (route name: `nl.home`)

You can also define a fallback route that will redirect to the localized route:

```php
Route::fallback(function () {
    return redirect()->localizedRoute('home');
});
```

### Translating route segments

You can translate each route segment by adding it to the configured translations file in the `lang` directory.

For example, if you defined a route `/about-us` and you support the locales `en` and `nl`,
you can add this to the configured translations files:

In `lang/en/routes.php`:

```php
return [
    'about-us' => 'about-us',
];
```

In `lang/nl/routes.php`:

```php
return [
    'about-us' => 'over-ons',
];
```

This will then generate the following routes:

- `http://example.com/en/about-us` (route name: `en.about-us`)
- `http://example.com/nl/over-ons` (route name: `nl.about-us`)

### Generating localized URLs

When using localized routes, you cannot use the `route()` helper to generate URLs.
Instead, you can use the `localizedRoute()` helper:

```php
localizedRoute('home'); // http://example.com/en (route name: en.home)
localizedRoute('blog.show', ['blog' => 'my-blog-post']); // http://example.com/en/blog/my-blog-post (route name: en.blog.show)
localizedRoute('home', [], 'nl'); // http://example.com/nl (route name: nl.home)
```

As you can see above, this one takes the same parameters as the `route()` helper.

### Getting the localized routes for the current route

You can get the localized routes for the current route like this:

```php
localization()->localizedRoutesForCurrentRoute();
```

This will for example return the following array if you are on the 'about-us' page:

```php
[
    'en' => 'http://example.com/en/about-us',
    'nl' => 'http://example.com/nl/over-ons',
]
```

This can be useful if you want to generate a language switcher.

### Getting the localized routes for a specific route

You can get the localized routes for a specific route like this:

```php
localization()->localizedRoutesForRoute('home');
```

In this example, the result would be:

```php
[
    'en' => 'http://example.com/en',
    'nl' => 'http://example.com/nl',
]
```

This can be useful if you want to tell Google about the other localized versions of a page.
(See [this article](https://developers.google.com/search/docs/specialty/international/localized-versions) for more information)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Simon Broekaert](https://github.com/SimonBroekaert)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
