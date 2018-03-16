<?php

namespace App\Commands\Install\Platforms;

use App\Commands\Install\BaseInstallCommand;

class InstallLaravelCommand extends BaseInstallCommand
{

    protected $signature = 'install:laravel {path : Where is the output folder?} {--r|remote : Load plate from online repository instead of local source?}';
    protected $description = '⏳  Install Laravel plus some Y7K sugar.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');

        $this->createDestinationPath($destinationPath);

        $this->info('Installing the Laravel Boilerplate...');

        $this->installRepositoryFromGitHub('laravel/laravel',[
            'destinationPath' => $destinationPath,
            'exclude' => ['resources/assets', 'public/css', 'public/js', 'resources/views'],
        ]);

       $this->installPlate([
           'destinationPath' => $destinationPath,
           'subfolders' => ['base', 'platforms/laravel']
       ], $this->option('remote'));

        $this->runPostInstallComposerCommands($destinationPath);

        $this->info('Installed the Laravel Boilerplate!');
    }

}
