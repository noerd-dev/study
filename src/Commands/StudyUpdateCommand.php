<?php

namespace Nywerk\Study\Commands;

class StudyUpdateCommand extends StudyInstallCommand
{
    protected $signature = 'noerd:update-study {--force : Overwrite existing files without asking}';

    protected $description = 'Update Study YML configuration files';

    public function handle(): int
    {
        return $this->runModuleUpdate();
    }
}
