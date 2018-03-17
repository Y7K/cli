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

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $destinationPath = $this->argument('path');
        $platform = strtolower($this->option('platform'));

        $this->line("");
        $this->line("Path set to <info>{$destinationPath}</info>.");
        $this->line("");

        $availablePlatforms = ['craft2', 'craft3', 'laravel', 'plain'];

        if(!in_array($platform, $availablePlatforms)) {
            $platform = $this->choice('Please select which type of application you\'re building', $availablePlatforms, 1);
        }

        $this->line("Platform set to <info>{$platform}</info>.");

        // Install repos

        $this->call('install:' . $platform, [
            'path' => $destinationPath,
            '--remote' => $this->option('remote')
        ]);

        $this->call('install:javascript', [
            'path' => $destinationPath,
            '--remote' => $this->option('remote')
        ]);

        $this->call('install:stylesheets', [
            'path' => $destinationPath,
            '--remote' => $this->option('remote')
        ]);

        // Init git and git flow
        $this->runProcessSequence([
            "cd {$destinationPath}",
            "git init",
            "printf 'master\ndevelop\nfeature/\nrelease/\nhotfix/\support/\n\n' | git-flow init",
            "git add --all",
            "git commit -m \"⚡️️ Initial Commit\"",
            "git branch -D master",
            "git branch master",
        ]);

        $this->line("Repository successfully initialized!");
    }

}
