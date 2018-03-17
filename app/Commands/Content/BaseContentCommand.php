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

}
