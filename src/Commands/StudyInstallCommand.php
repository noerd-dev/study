<?php

declare(strict_types=1);

namespace Nywerk\Study\Commands;

use Illuminate\Console\Command;
use Noerd\Traits\HasModuleInstallation;

class StudyInstallCommand extends Command
{
    use HasModuleInstallation;

    protected $signature = 'noerd:install-study {--force : Overwrite existing files without asking}';

    protected $description = 'Install noerd Study module';

    public function handle(): int
    {
        return $this->runModuleInstallation();
    }

    protected function getModuleName(): string
    {
        return 'Study';
    }

    protected function getModuleKey(): string
    {
        return 'study';
    }

    protected function getDefaultAppTitle(): string
    {
        return 'Study';
    }

    protected function getAppIcon(): string
    {
        return 'study::icons.app';
    }

    protected function getAppRoute(): string
    {
        return 'study.dashboard';
    }

    protected function getSourceDir(): string
    {
        return dirname(__DIR__, 2) . '/app-configs/study';
    }

}
