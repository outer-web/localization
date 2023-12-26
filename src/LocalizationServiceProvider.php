<?php

namespace Outerweb\Localization;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Outerweb\Localization\Services\Localization;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LocalizationServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        Redirect::macro('localizedRoute', function (string $name, array $parameters = [], bool $absolute = true, ?string $locale = null) {
            return redirect()->to(localization()->localizedRoute($name, $parameters, $absolute, $locale));
        });

        Route::macro('localized', function ($callback) {
            localization()->registerRoutes($callback);
        });
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(Localization::class, fn () => new Localization);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('localization')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $composerFile = file_get_contents(__DIR__.'/../composer.json');

                if ($composerFile) {
                    $githubRepo = json_decode($composerFile, true)['homepage'] ?? null;

                    if ($githubRepo) {
                        $command
                            ->askToStarRepoOnGitHub($githubRepo);
                    }
                }
            });
    }
}
