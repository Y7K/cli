<?php

namespace App\Commands\Install\Platforms;

use App\Commands\BaseCommand;
use App\Concerns\InteractsWithGit;
use App\Concerns\InteractsWithProjectJsonFile;

class InstallCraft2Command extends BaseCommand
{

    use InteractsWithProjectJsonFile, InteractsWithGit;

    protected $signature = 'install:craft2 {path? : Where is the output folder?} {--r|remote : Load plate from online repository instead of local source?}';
    protected $description = '⏳  Install Craft 2.* plus some Y7K sugar.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

    }

}
