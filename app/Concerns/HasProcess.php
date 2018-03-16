<?php
namespace App\Concerns;


use Symfony\Component\Process\Process;

trait HasProcess
{

    public function runProcess(string $command)
    {
        $process = new Process($command);
        $process->run();
        return $process->getOutput();
    }

    public function runProcessSequence(array $commands)
    {
        return $this->runProcess(implode(' && ', $commands));
    }

}
