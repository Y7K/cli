<?php
/**
 * Created by PhpStorm.
 * User: joris
 * Date: 16.03.18
 * Time: 18:30
 */

namespace App\Commands\Content\Assets;


class AssetsPushCommand extends BaseAssetsCommand
{

    protected $signature = 'assets:push {environment : Environment name (defined in .y7k-cli.json)} {--f|force}';
    protected $description = '⬆  Push the assets from local to a specified environment';

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

        $this->warn("Uploading assets: Permanently <fg=red>overwrite</> data on  ({$environment}) with (local).");

        $this->confirmAction($remoteEnv, $this->option('force'), 'assets');

        $command = $this->buildRsyncCommand($localEnv['storage'], "{$remoteEnv['sshuser']}@{$remoteEnv['host']}:{$remoteEnv['storage']}");

        $this->runProcess($command);
    }


}
