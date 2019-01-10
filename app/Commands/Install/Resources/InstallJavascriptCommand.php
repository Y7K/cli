<?php

namespace App\Commands\Install\Resources;

use App\Commands\Install\BaseInstallCommand;
use App\Helpers\FileMergeHelper;

class InstallJavascriptCommand extends BaseInstallCommand
{

    protected $signature = 'install:javascript {path : Where is the output folder?} {--l|local : Load from local repository instead of remote source?}';
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

        $this->task("Install the <fg=green>{$this->packageName}</> boilerplate", function () use ($destinationPath, $assetsPath) {

           $this->installY7KRepo('scripts', [
               'destinationPath' => $assetsPath,
               'subfolders' => ['src']
           ], $this->option('local'));

            $this->task('Merge package.json', function () use ($destinationPath, $assetsPath) {
                FileMergeHelper::mergeJsonFiles($destinationPath . '/package.json', $assetsPath . '/package.json');
                unlink($assetsPath . '/package.json');
            });

        });
    }

}
