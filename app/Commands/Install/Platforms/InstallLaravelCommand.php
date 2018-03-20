<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;
use App\Helpers\FileMergeHelper;

class InstallLaravelCommand extends BaseInstallCommand
{

    protected $signature = 'install:laravel {path : Where is the output folder?} {--r|remote : Load from online repository instead of local source?}';
    protected $description = '⏳  Install Laravel plus some Y7K sugar.';
    protected $packageName = 'Laravel';

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

            $this->installRepositoryFromGitHub('laravel/laravel', [
                'destinationPath' => $destinationPath,
                'exclude' => ['resources/assets', 'public/css', 'public/js', 'resources/views'],
            ]);

            $this->installY7KRepo('plate', [
                'destinationPath' => $destinationPath,
                'subfolders' => ['base', 'platforms/laravel']
            ], $this->option('remote'));

            $this->task("Merge composer.json", function () use ($destinationPath) {
                FileMergeHelper::mergeJsonFiles($destinationPath . '/composer.json', $destinationPath . '/composer.merge.json');
                unlink($destinationPath . '/composer.merge.json');
            });

            $this->runPostInstallComposerCommands($destinationPath);

        });

        $this->info("Installed the {$this->packageName} boilerplate!");
    }

}
