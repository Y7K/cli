<?php

namespace App\Commands\Install\Resources;

use App\Commands\Install\BaseInstallCommand;

class InstallStylesheetsCommand extends BaseInstallCommand
{

    protected $signature = 'install:stylesheets {path : Where is the output folder?} {--r|remote : Load from online repository instead of local source?}';
    protected $description = '⏳  Install the Stylesheets boilerplate.';
    protected $packageName = 'Stylesheets';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $assetsPath = $destinationPath . '/resources/assets';

        $this->createDestinationPath($assetsPath);

        $this->info("Installing the {$this->packageName} boilerplate...");

       $this->installY7KRepo('styles', [
           'destinationPath' => $assetsPath,
           'subfolders' => ['source']
       ], $this->option('remote'));

        $this->info("Installed the {$this->packageName} boilerplate!");
    }

}
