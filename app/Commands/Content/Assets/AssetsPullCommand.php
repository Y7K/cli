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

        // Validate Config
        $localEnv = $this->getValidatedEnvironmentData('local', ['storage']);
        $remoteEnv = $this->getValidatedEnvironmentData($environment, ['host', 'sshuser', 'path', 'storage']);

        // Ask for confirmation
        $this->confirmSyncingContent('assets', $environment, 'local', $this->option('force'));


        $this->task("Downloading assets", function () use ($environment, $localEnv) {
            // Execute Command
            $remoteStoragePath = $this->buildRemoteStoragePath($environment);

            $command = $this->buildRsyncCommand(
                $remoteStoragePath,
                $localEnv['storage']
            );

            $this->runProcess($command);
        });
    }


}
