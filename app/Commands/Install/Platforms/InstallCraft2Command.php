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

        // check if destination folder exists

        // install y7k plate
        $this->installRepositoryFromGitHub('y7k/plate', $destinationPath, []);

        if($this->option('remote')) {

        } else {

        }

    }

}
