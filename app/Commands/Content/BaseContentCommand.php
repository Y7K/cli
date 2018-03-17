<?php

namespace App\Commands\Content;

use App\Commands\BaseCommand;
use App\Concerns\HasProcess;
use App\Concerns\ReadsY7KCliConfigFile;

abstract class BaseContentCommand extends BaseCommand
{

    use ReadsY7KCliConfigFile, HasProcess;

    public function confirmAction($destinationEnv, $force, $type)
    {
        if ($this->isProduction($destinationEnv)) {
            $fuckingsure = $this->ask("This will <fg=red>OVERWRITE</> production {$type}! Are you really sure? Type <bg=yellow>i fucking know what im doing</> if you want to proceed");
            if (trim(strtolower($fuckingsure)) !== 'i fucking know what im doing') {
                $this->abort('Aborted.');
            }
        } else if (!$force && !$this->confirm('Are you really sure you wish to continue?')) {
            $this->abort('Aborted.');
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
