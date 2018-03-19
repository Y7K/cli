<?php

namespace App\Commands\Content\Assets;


use App\Commands\Content\BaseContentCommand;

class AssetsPushCommand extends BaseContentCommand
{

    protected $signature = 'assets:push {environment : Environment name (defined in .y7k-cli.json)}';
    protected $description = '⬆  Push the assets from local to a specified environment';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $environment = $this->argument('environment');

        // Validate Config
        $localEnv = $this->getValidatedEnvironmentData('local', ['storage']);
        $remoteEnv = $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'storage']);

        $this->line("");
        $this->warn("Uploading assets: Permanently <fg=red>overwrite</> data on ({$environment}) with (local).");

        // Ask for confirmation
        $this->confirmSyncingContent($environment, false, 'assets');

        // Execute Command
        $remoteStoragePath = $this->buildRemoteStoragePath($environment);

        $command = $this->buildRsyncCommand(
            $localEnv['storage'],
            $remoteStoragePath
        );

        $this->runProcess($command);

        $this->info("Assets on (local) are now in sync with ({$environment})!");
    }


}
