<?php

namespace App\Commands\Content;

use App\Commands\BaseCommand;
use App\Concerns\HasProcess;
use App\Concerns\ReadsY7KCliConfigFile;

abstract class BaseContentCommand extends BaseCommand
{

    use ReadsY7KCliConfigFile, HasProcess;

    public function confirmAction($remoteEnv, $force, $type)
    {
        if (isset($remoteEnv['production']) && $remoteEnv['production']) {
            $fuckingsure = $this->ask("This will <fg=red>OVERWRITE</> production {$type}! Are you really sure? Type <bg=yellow>i fucking know what im doing</> if you want to proceed.");
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

        return
            "{$sourceSsh} \"mysqldump --opt --user={$sourceEnv['dbuser']} --password={$sourceEnv['dbpassword']} {$sourceEnv['db']}\"" .
            " | {$destinationSsh} \"mysql --user={$destinationEnv['dbuser']} --password={$destinationEnv['dbpassword']} {$destinationEnv['db']}\"";
    }

    public function buildSshCommand($env)
    {
        $port = (isset($env['port']) && $env['port']) ? " -p " . $env['port'] : '';
        return "ssh {$env['sshuser']}@{$env['host']}{$port}";
    }

    public function buildStoragePath($env)
    {
        $remotePath = rtrim($env['path'], '/');
        $remoteStorage = trim($env['storage'], '/');
        return rtrim($remotePath . '/' . $remoteStorage, '/');
    }

}
