<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallCraft3Command extends BaseInstallCommand
{

    protected $signature = 'install:craft3 {path : Where is the output folder?} {--r|remote : Load from online repository instead of local source?}';
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

        $this->createDestinationPath($destinationPath);

        $this->info("Installing the {$this->packageName} boilerplate...");

       $this->installY7KRepo('plate', [
           'destinationPath' => $destinationPath,
           'subfolders' => ['base', 'platforms/craft3']
       ], $this->option('remote'));

        $this->runPostInstallComposerCommands($destinationPath);

        $this->info("Installed the {$this->packageName} boilerplate!");
    }

}
