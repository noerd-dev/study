<?php

namespace Nywerk\Study\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Noerd\Services\RelationFieldRegistry;
use Noerd\Support\RelationFieldDefinition;
use Nywerk\Study\Commands\StudyInstallCommand;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Nywerk\Study\Commands\StudyUpdateCommand;

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
        Livewire::addNamespace('study', viewPath: __DIR__ . '/../../resources/views/components');
        Livewire::addLocation(viewPath: __DIR__ . '/../../resources/views/components');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../resources/lang');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/study-routes.php');

        $this->ensureFontsDirectoryExists();

        if ($this->app->runningInConsole()) {
            $this->commands([
                StudyInstallCommand::class,
                StudyUpdateCommand::class,
            ]);
        }

        $relationFieldRegistry = $this->app->make(RelationFieldRegistry::class);
        $relationFieldRegistry->register('studyMaterialRelation', RelationFieldDefinition::model(
            listComponent: 'study::study-materials-list',
            detailComponent: 'study::study-material-detail',
            modelClass: StudyMaterial::class,
            titleResolver: 'title',
        ));
        $relationFieldRegistry->register('summaryRelation', RelationFieldDefinition::model(
            listComponent: 'study::summaries-list',
            detailComponent: 'study::summary-detail',
            modelClass: Summary::class,
            titleResolver: 'title',
        ));
    }

    private function ensureFontsDirectoryExists(): void
    {
        $fontsPath = storage_path('fonts');

        if (! is_dir($fontsPath)) {
            mkdir($fontsPath, 0755, true);
        }
    }
}
