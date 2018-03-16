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

        if (!mkdir($destinationPath) && !is_dir($destinationPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" already exists or could not be created', $destinationPath));
        }

        // check if destination folder exists

        // install y7k plate
        $this->installRepositoryFromGitHub('y7k/plate', [
            'destinationPath' => $destinationPath,
            'subfolders' => ['base', 'platforms/craft'],
            'excluded' => ['base/public']
        ]);

        if($this->option('remote')) {

        } else {

        }

    }

}
