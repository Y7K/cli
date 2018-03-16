<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallCraft3Command extends BaseInstallCommand
{

    protected $signature = 'install:craft3 {path : Where is the output folder?} {--r|remote : Load plate from online repository instead of local source?}';
    protected $description = '⏳  Install Craft 3.* plus some Y7K sugar.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $this->createDestinationPath($destinationPath);

        $this->info('Installing the Craft CMS 3.* Boilerplate...');

       $this->installPlate([
           'destinationPath' => $destinationPath,
           'subfolders' => ['base', 'platforms/craft3']
       ], $this->option('remote'));

        $this->runPostInstallComposerCommands($destinationPath);

        $this->info('Installed the Craft CMS 3.* Boilerplate!');
    }

}
