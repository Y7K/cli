<?php

namespace App\Commands\Install\Resources;

use App\Commands\Install\BaseInstallCommand;
use App\Helpers\JsonHelper;

class InstallJavascriptCommand extends BaseInstallCommand
{

    protected $signature = 'install:javascript {path : Where is the output folder?} {--r|remote : Load from online repository instead of local source?}';
    protected $description = '⏳  Install the Javascript boilerplate.';
    protected $packageName = 'JavaScript';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $assetsPath = $destinationPath . '/resources/assets';

        $this->abortIfDirectoryExists($assetsPath . '/js', false);

        $this->info("Installing the {$this->packageName} boilerplate...");

       $this->installY7KRepo('scripts', [
           'destinationPath' => $assetsPath,
           'subfolders' => ['source']
       ], $this->option('remote'));

        JsonHelper::mergeJsonFiles($destinationPath . '/package.json', $assetsPath . '/package.json');

        $this->info("Installed the {$this->packageName} boilerplate!");
    }

}
