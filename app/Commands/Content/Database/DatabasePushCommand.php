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

        $this->warn("Uploading database: Permanently <fg=red>overwrite</> data on  ({$environment}) with (local).");

        $this->confirmAction($remoteEnv, false, 'database');

        $command = $this->buildRsyncCommand(
            $localEnv['storage'], "{$remoteEnv['sshuser']}@{$remoteEnv['host']}:{$remoteEnv['storage']}");

        $this->runProcess($command);
    }


}
