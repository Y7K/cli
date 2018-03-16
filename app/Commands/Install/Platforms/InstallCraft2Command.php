<?php

namespace App\Commands\Install\Platforms;

use App\Commands\BaseCommand;
use App\Concerns\InstallsRepository;

class InstallCraft2Command extends BaseCommand
{

    use InstallsRepository;

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

        // Check if destination folder exists
        if (!is_dir($destinationPath) && !mkdir($destinationPath) && !is_dir($destinationPath)) {
            $this->abort("Directory {$destinationPath} already exists or could not be created");
        }

        $installRepositorycommand = ($this->option('remote'))
            ? 'installRepositoryFromGitHub' : 'installRepositoryFromLocalSource';

        // Install y7k plate
        $this->{$installRepositorycommand}('y7k/plate', [
            'destinationPath' => $destinationPath,
            'subfolders' => ['base', 'platforms/craft'],
            'excluded' => ['base/project.json']
        ]);

    }

}
