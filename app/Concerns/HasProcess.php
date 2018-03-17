<?php
namespace App\Concerns;


use Symfony\Component\Process\Process;

trait HasProcess
{

    public function runProcess(string $command)
    {
//        var_dump($this->verbosity);
//        $this->line("Executing command: \"{$command}\"");

        $process = new Process($command);
        $process->run(function ($type, $buffer) {
            $buffer = str_replace(array("\r", "\n"), '', $buffer);
            $this->line($buffer);
        });

        return $process->getOutput();
    }

    public function runProcessSequence(array $commands)
    {
        return $this->runProcess(implode(' && ', $commands));
    }

}
