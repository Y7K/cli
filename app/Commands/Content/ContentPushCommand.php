<?php

namespace App\Commands\Content;


class ContentPushCommand extends BaseContentCommand
{

    protected $signature = 'c:push {environment : Environment name (defined in .y7k-cli.json)}';
    protected $description = '⬆  Push both database and assets from local to a specified environment';

    public function handle(): void
    {

        $environment = $this->argument('environment');

        $this->line("");
        $this->warn("Uploading assets and database: Permanently <fg=red>overwrite</> ({$environment}) data with (local).");


        $this->call('db:push', [
            'environment' => $environment
        ]);

        $this->call('assets:push', [
            'environment' => $environment
        ]);

    }

}
