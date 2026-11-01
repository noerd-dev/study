<?php

declare(strict_types=1);

namespace Nywerk\Study\Commands;

use Illuminate\Console\Command;
use Noerd\Commands\Concerns\HasModuleInstallation;

class StudyInstallCommand extends Command
{
    use HasModuleInstallation;

    protected $signature = 'noerd:install-study';

    protected $description = 'Install noerd Study module';

    protected function getModuleName(): string
    {
        return 'Study';
    }

    protected function getModuleKey(): string
    {
        return 'study';
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
