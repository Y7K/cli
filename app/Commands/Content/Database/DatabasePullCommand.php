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
        $this->getValidatedEnvironmentData('local', ['host', 'sshuser', 'dbuser', 'dbpassword', 'db']);
        $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'dbuser', 'dbpassword', 'db']);

        $this->line("");
        $this->warn("Downloading database: Permanently <fg=red>overwrite</> (local) data with ({$environment}).");

        // Ask for confirmation
        $this->confirmSyncingContent('local', $this->option('force'), 'database');

        // Execute Command
        $command = $this->buildMysqldumpCommand($environment, 'local');

        $this->runProcess($command);

        $this->info("Database on (local) is now in sync with ({$environment})!");
    }


}
