<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallCraft3Command extends BaseInstallCommand
{

    protected $signature = 'install:craft3 {path : Where is the output folder?} {--l|local : Load from local repository instead of remote source?}';
    protected $description = '⏳  Install Craft 3.* plus some Y7K sugar.';
    protected $packageName = 'Craft 3';

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
                'subfolders' => ['base', 'platforms/craft3']
            ], $this->option('local'));

            $this->runPostInstallComposerCommands($destinationPath);

        });

    }

}
