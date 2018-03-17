<?php

namespace App\Commands\Content\Assets;


use App\Commands\Content\BaseContentCommand;

class AssetsPullCommand extends BaseContentCommand
{

    protected $signature = 'assets:pull {environment : Environment name (defined in .y7k-cli.json)} {--f|force}';
    protected $description = '⬇  Pull the assets from a specified environment to local';

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
        $this->warn("Downloading assets: Permanently <fg=red>overwrite</> (local) data with ({$environment}).");

        $this->confirmAction($remoteEnv, $this->option('force'), 'assets');

        $command = $this->buildRsyncCommand(
            "{$remoteEnv['sshuser']}@{$remoteEnv['host']}:{$remoteEnv['storage']}",
            $localEnv['storage']
        );

        $this->runProcess($command);

        $this->info("Assets on (local) are now in sync with ({$environment})!");
    }


}
