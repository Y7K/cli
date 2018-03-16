<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallPlainCommand extends BaseInstallCommand
{

    protected $signature = 'install:plain {path : Where is the output folder?} {--r|remote : Load plate from online repository instead of local source?}';
    protected $description = '⏳  Install the plain Y7K boilerplate.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $this->createDestinationPath($destinationPath);

        $this->info('Installing the Plain Boilerplate...');

       $this->installPlate([
           'destinationPath' => $destinationPath,
           'subfolders' => ['base', 'platforms/plain']
       ], $this->option('remote'));

        $this->runPostInstallComposerCommands($destinationPath);

        $this->info('Installed the Plain Boilerplate!');
    }

}
