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

        $localEnv = $this->getValidatedEnvironmentData('local', ['storage']);
        $remoteEnv = $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'storage']);

        $this->line("");
        $this->warn("Uploading database: Permanently <fg=red>overwrite</> data on ({$environment}) with (local).");

        $this->confirmAction($remoteEnv, false, 'database');

        $command = $this->buildMysqldumpCommand($localEnv, $remoteEnv);

        $this->runProcess($command);

        $this->info("Databse on ({$environment}) is now in sync with (local)!");
    }


}
