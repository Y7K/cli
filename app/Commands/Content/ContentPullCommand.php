<?php

namespace App\Commands\Content;


class ContentPullCommand extends BaseContentCommand
{

    protected $signature = 'c:pull {environment : Environment name (defined in .y7k-cli.json)} {--f|force}';
    protected $description = '⬇  Pull both database and assets from a specified environment to local';

    public function handle(): void
    {

        $this->call('db:pull', [
            'environment' => $this->argument('environment'),
            '--force' => $this->option('force')
        ]);

        $this->call('assets:pull', [
            'environment' => $this->argument('environment'),
            '--force' => $this->option('force')
        ]);

    }

}
