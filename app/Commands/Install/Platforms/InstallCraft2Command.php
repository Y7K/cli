<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallCraft2Command extends BaseInstallCommand
{

    protected $signature = 'install:craft2 {path : Where is the output folder?} {--r|remote : Load from online repository instead of local source?}';
    protected $description = '⏳  Install Craft 2.* plus some Y7K sugar.';
    protected $packageName = 'Craft 2';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $this->abortIfDirectoryExists($destinationPath);

        $this->info("Installing the {$this->packageName} boilerplate...");

       $this->installY7KRepo('plate', [
           'destinationPath' => $destinationPath,
           'subfolders' => ['base', 'platforms/craft']
       ], $this->option('remote'));

        // Install Craft App folder
        $this->installRepositoryFromUrl('http://craftcms.com/latest.zip?accept_license=yes', [
            'destinationPath' => $destinationPath . '/craft/app',
            'subfolders' => ['craft/app']
        ]);

        $this->runPostInstallComposerCommands($destinationPath);

        $this->info("Installed the {$this->packageName} boilerplate!");
    }

}
