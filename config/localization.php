<?php

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
