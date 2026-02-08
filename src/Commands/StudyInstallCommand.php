<?php

namespace Nywerk\Study\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Noerd\Traits\HasModuleInstallation;
use Noerd\Traits\RequiresNoerdInstallation;

class StudyInstallCommand extends Command
{
    use HasModuleInstallation;
    use RequiresNoerdInstallation;

    protected $signature = 'noerd:install-study {--force : Overwrite existing files without asking}';

    protected $description = 'Install noerd Study module';

    public function handle(): int
    {
        $result = $this->runModuleInstallation();

        if ($result === 0) {
            $this->registerModule();
        }

        return $result;
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

    protected function getSnippetTitle(): string
    {
        return 'Study';
    }

    /**
     * @return array<string>
     */
    protected function getAdditionalSubdirectories(): array
    {
        return [];
    }

    /**
     * Register the Study module with Composer.
     */
    private function registerModule(): void
    {
        $this->line('');
        $this->info('Registering Study module...');

        try {
            // Run composer require to register the module
            $this->line('<comment>Running composer require noerd/study...</comment>');
            exec('composer require noerd/study 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                $this->warn('Composer require failed, trying composer dump-autoload...');
            }

            // Run composer dump-autoload
            $this->line('<comment>Running composer dump-autoload...</comment>');
            exec('composer dump-autoload 2>&1', $dumpOutput, $dumpReturnCode);

            // Clear Laravel caches
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            // Remove services cache to force re-discovery
            $servicesCache = base_path('bootstrap/cache/services.php');
            if (file_exists($servicesCache)) {
                unlink($servicesCache);
            }

            $this->line('<info>Study module registered successfully.</info>');
        } catch (Exception $e) {
            $this->warn('Module registration failed: ' . $e->getMessage());
        }
    }
}
