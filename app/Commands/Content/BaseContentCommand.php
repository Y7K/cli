<?php

namespace App\Commands\Content;

use App\Commands\BaseCommand;
use App\Concerns\HasProcess;
use App\Concerns\ReadsY7KCliConfigFile;

abstract class BaseContentCommand extends BaseCommand
{

    use ReadsY7KCliConfigFile, HasProcess;

    public function confirmSyncingContent($type, $sourceEnv, $destinationEnv, $force)
    {

        $this->line("");

        if ($this->isProduction($destinationEnv)) {

            $this->warn("Permanently <fg=red>OVERWRITE production</> {$type}!");
            $fuckingsure = $this->ask("Are you really sure? Type <bg=yellow><fg=black>i fucking know what im doing</></> if you want to proceed");
            if (trim(strtolower($fuckingsure)) !== 'i fucking know what im doing') {
                $this->abort('Aborted.');
            }

        } else {

            $this->warn("Permanently <fg=red>overwrite</> <fg=blue>({$destinationEnv})</> {$type} with <fg=blue>({$sourceEnv})</>.");
            if (!$force && !$this->confirm('Are you sure you wish to continue?')) {
                $this->abort('Aborted.');
            }
        }

    }

    public function buildRsyncCommand($source, $destination)
    {
        $source = rtrim($source, '/');
        $destination = rtrim($destination, '/');
        return "rsync -avz --delete-excluded --include=\".git*\" --exclude=\".*\" {$source}/ {$destination}";
    }

    public function buildMysqldumpCommand($sourceEnv, $destinationEnv)
    {
        $sourceSsh = $this->buildSshCommand($sourceEnv);
        $destinationSsh = $this->buildSshCommand($destinationEnv);
        $sourceData = $this->getCliEnvironmentData($sourceEnv);
        $destinationData = $this->getCliEnvironmentData($destinationEnv);

        return
            "{$sourceSsh} \"mysqldump --opt --user={$sourceData['dbuser']} --password={$sourceData['dbpassword']} {$sourceData['db']}\"" .
            " | {$destinationSsh} \"mysql --user={$destinationData['dbuser']} --password={$destinationData['dbpassword']} {$destinationData['db']}\"";
    }

}
