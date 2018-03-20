<?php

namespace App\Commands;


use App\Concerns\HasProcess;

class NewCommand extends BaseCommand
{

    use HasProcess;

    protected $signature = 'new ' .
    '{path : Choose a folder, I\'ll take care of the rest.} ' .
    '{--p|platform : Which Type shall it be: Craft, Laravel or a static Site?} ' .
    '{--r|remote : Load from online repository instead of local source?}';
    protected $description = '👻  Install a shiny new Project';

    protected $destinationPath;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->destinationPath = $this->argument('path');

        $this
            ->installPlatform()
            ->installResources()
            ->initialiseRepository();
    }

    public function installPlatform()
    {
        $platform = strtolower($this->option('platform'));

        $this->line("");
        $this->line("Path set to <info>{$this->destinationPath}</info>.");
        $this->line("");

        $availablePlatforms = ['craft2', 'craft3', 'laravel', 'plain'];

        if(!in_array($platform, $availablePlatforms)) {
//            $platform = $this->choice('Please select which type of application you\'re building', $availablePlatforms, 1);
            $platform = $this->menu('Application Type', $availablePlatforms)->open();
            if(!$platform) $this->abort("Aborted.");
        }

        $this->line("Platform set to <info>{$platform}</info>.");

        // Install repo
        $this->call('install:' . $platform, [
            'path' => $this->destinationPath,
            '--remote' => $this->option('remote')
        ]);

        return $this;
    }

    public function installResources()
    {
        $this->call('install:javascript', [
            'path' => $this->destinationPath,
            '--remote' => $this->option('remote')
        ]);

        $this->call('install:stylesheets', [
            'path' => $this->destinationPath,
            '--remote' => $this->option('remote')
        ]);

        return $this;
    }

    public function initialiseRepository()
    {
        // Init git and git flow
        $this->runProcessSequence([
            "cd {$this->destinationPath}",
            "git init",
            "printf 'master\ndevelop\nfeature/\nrelease/\nhotfix/\support/\n\n' | git-flow init",
            "git add --all",
            "git commit -m \"⚡️️ Initial Commit\"",
            "git branch -D master",
            "git branch master",
        ], true);

        $this->line("Repository successfully initialized!");

        return $this;
    }

}
