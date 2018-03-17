<?php

namespace App\Commands\Content;


class ContentPushCommand extends BaseContentCommand
{

    protected $signature = 'c:push {environment : Environment name (defined in .y7k-cli.json)}';
    protected $description = '⬆  Push both database and assets from local to a specified environment';

    public function handle(): void
    {

        $this->call('db:push', [
            'environment' => $this->argument('environment')
        ]);

        $this->call('assets:push', [
            'environment' => $this->argument('environment')
        ]);

    }

}
