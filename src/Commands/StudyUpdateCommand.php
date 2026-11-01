<?php

declare(strict_types=1);

namespace Nywerk\Study\Commands;

class StudyUpdateCommand extends StudyInstallCommand
{
    protected $signature = 'noerd:update-study';

    protected $description = 'Update Study YML configuration files';
}
