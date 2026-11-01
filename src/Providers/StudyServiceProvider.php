<?php

namespace Nywerk\Study\Providers;

use Noerd\Providers\NoerdModuleServiceProvider;
use Noerd\Support\RelationFieldDefinition;
use Nywerk\Study\Commands\StudyInstallCommand;
use Nywerk\Study\Commands\StudyUpdateCommand;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

class StudyServiceProvider extends NoerdModuleServiceProvider
{
    protected string $moduleKey = 'study';

    protected array $commands = [
        StudyInstallCommand::class,
        StudyUpdateCommand::class,
    ];

    protected function relationFields(): array
    {
        return [
            'studyMaterialRelation' => RelationFieldDefinition::model(
                listComponent: 'study::study-materials-list',
                detailComponent: 'study::study-material-detail',
                modelClass: StudyMaterial::class,
                titleResolver: 'title',
            ),
            'summaryRelation' => RelationFieldDefinition::model(
                listComponent: 'study::summaries-list',
                detailComponent: 'study::summary-detail',
                modelClass: Summary::class,
                titleResolver: 'title',
            ),
        ];
    }

    protected function bootModule(): void
    {
        $this->ensureFontsDirectoryExists();
    }

    /**
     * Dompdf caches the font metrics of the flashcard PDF in storage/fonts but
     * never creates the directory itself.
     */
    private function ensureFontsDirectoryExists(): void
    {
        $fontsPath = storage_path('fonts');

        if (! is_dir($fontsPath)) {
            mkdir($fontsPath, 0755, true);
        }
    }
}
