<?php

namespace App\Commands\Content\Database;


use App\Commands\Content\BaseContentCommand;

class DatabasePullCommand extends BaseContentCommand
{

    protected $signature = 'db:pull {environment : Environment name (defined in .y7k-cli.json)} {--f|force}';
    protected $description = '⬇  Pull the database from a specified environment to local';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $environment = $this->argument('environment');

        // Validate Config
        $this->getValidatedEnvironmentData('local', ['host', 'dbuser', 'db']);
        $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'dbuser', 'db']);

        // Ask for confirmation
        $this->confirmSyncingContent('database', $environment, 'local', $this->option('force'));

        $this->task("Downloading database", function () use ($environment) {

            // Execute Command
            $command = $this->buildMysqldumpCommand($environment, 'local');
            $this->runProcess($command);

            return true;
        });
    }


}
