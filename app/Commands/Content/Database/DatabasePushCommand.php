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
        $this->getValidatedEnvironmentData('local', ['host', 'sshuser', 'dbuser', 'dbpassword', 'db']);
        $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'dbuser', 'dbpassword', 'db']);

        $this->line("");
        $this->warn("Uploading database: Permanently <fg=red>overwrite</> data on ({$environment}) with (local).");

        // Ask for confirmation
        $this->confirmSyncingContent($environment, false, 'database');

        // Execute Command
        $command = $this->buildMysqldumpCommand('local', $environment);

        $this->runProcess($command);

        $this->info("Databse on ({$environment}) is now in sync with (local)!");
    }


}
