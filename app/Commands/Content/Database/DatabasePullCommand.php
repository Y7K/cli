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

        $localEnv = $this->getValidatedEnvironmentData('local', ['storage']);
        $remoteEnv = $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'storage']);

        $this->warn("Downloading database: Permanently <fg=red>overwrite</> (local) data with ({$environment}).");

        $this->confirmAction($remoteEnv, $this->option('force'), 'database');

        $command = $this->buildMysqldumpCommand($remoteEnv, $localEnv);

        $this->runProcess($command);

        $this->info("Database on (local) is now in sync with ({$environment})!");
    }


}
