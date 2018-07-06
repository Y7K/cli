<?php

namespace App\Commands\Content\Database;


use App\Commands\Content\BaseContentCommand;

class DatabasePushCommand extends BaseContentCommand
{

    protected $signature = 'db:push {environment : Environment name (defined in .y7k-cli.json)}';
    protected $description = '⬆  Push the database from local to a specified environment';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $environment = $this->argument('environment');

        // Validate Config
        $this->getValidatedEnvironmentData('local', ['host', 'sshuser', 'dbuser', 'db']);
        $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'dbuser', 'db']);

        // Ask for confirmation
        $this->confirmSyncingContent('database', 'local', $environment, false);

        $this->task("Uploading database", function () use ($environment) {
            // Execute Command
            $command = $this->buildMysqldumpCommand('local', $environment);

            $this->runProcess($command);
            return true;
        });

    }


}
