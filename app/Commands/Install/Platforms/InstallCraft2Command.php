<?php

namespace App\Commands\Install\Platforms;

use App\Commands\BaseCommand;
use App\Concerns\InstallsRepository;

class InstallCraft2Command extends BaseCommand
{

    use InstallsRepository;

    protected $signature = 'install:craft2 {path? : Where is the output folder?} {--r|remote : Load plate from online repository instead of local source?}';
    protected $description = '⏳  Install Craft 2.* plus some Y7K sugar.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

        $this->installRepositoryFromGitHub('y7k/plate/master', 'test', []);

        if($this->option('remote')) {
        } else {

        }

    }

}
