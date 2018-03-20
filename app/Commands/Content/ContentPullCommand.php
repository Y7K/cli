<?php

namespace App\Commands\Content;


class ContentPullCommand extends BaseContentCommand
{

    protected $signature = 'c:pull {environment : Environment name (defined in .y7k-cli.json)} {--f|force}';
    protected $description = '⬇  Pull both database and assets from a specified environment to local';

    public function handle(): void
    {
        $environment = $this->argument('environment');

        $this->confirmSyncingContent('assets and database', $environment, 'local', $this->option('force'));

        $this->call('db:pull', [
            'environment' => $environment,
            '--force' => true
        ]);

        $this->call('assets:pull', [
            'environment' => $environment,
            '--force' => true
        ]);

    }

}
