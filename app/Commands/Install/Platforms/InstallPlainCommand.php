<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallPlainCommand extends BaseInstallCommand
{

    protected $signature = 'install:plain {path : Where is the output folder?} {--l|local : Load from local repository instead of remote source?}';
    protected $description = '⏳  Install the plain Y7K boilerplate.';
    protected $packageName = 'Plain';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $this->abortIfDirectoryExists($destinationPath);

        $this->task("Install the <fg=green>{$this->packageName}</> boilerplate", function () use ($destinationPath) {

            $this->installY7KRepo('plate', [
                'destinationPath' => $destinationPath,
                'subfolders' => ['base', 'platforms/plain']
            ], $this->option('local'));

            $this->runPostInstallComposerCommands($destinationPath);

        });
    }

}
