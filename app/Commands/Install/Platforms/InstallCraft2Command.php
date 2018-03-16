<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallCraft2Command extends BaseInstallCommand
{

    protected $signature = 'install:craft2 {path : Where is the output folder?} {--r|remote : Load plate from online repository instead of local source?}';
    protected $description = '⏳  Install Craft 2.* plus some Y7K sugar.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $this->createDestinationPath($destinationPath);

        $this->info('Installing the Craft CMS 2.* Boilerplate...');

       $this->installPlate([
           'destinationPath' => $destinationPath,
           'subfolders' => ['base', 'platforms/craft']
       ], $this->option('remote'));

        // Install Craft App folder
        $this->installRepositoryFromUrl('http://craftcms.com/latest.zip?accept_license=yes', [
            'destinationPath' => $destinationPath . '/craft/app',
            'subfolders' => ['craft/app']
        ]);

        $this->runPostInstallComposerCommands($destinationPath);

        $this->info('Installed the Craft CMS 2.* Boilerplate!');
    }

}
