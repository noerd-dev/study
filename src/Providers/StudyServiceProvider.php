<?php

namespace Nywerk\Study\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Nywerk\Study\Commands\StudyInstallCommand;

class StudyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'study');
        Livewire::addLocation(viewPath: __DIR__ . '/../../resources/views/components');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../resources/lang');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/study-routes.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                StudyInstallCommand::class,
            ]);
        }
    }
}
