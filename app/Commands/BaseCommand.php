<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

abstract class BaseCommand extends Command
{

    public function abort($message)
    {
        $this->error($message);die;
    }

    public function runProcess($command)
    {
        $process = new Process($command);
        $process->run();
        return $process->getOutput();
    }

    public function getWorkingDirectory()
    {
        return getcwd();
    }

}
