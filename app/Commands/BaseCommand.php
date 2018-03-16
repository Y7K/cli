<?php

namespace App\Commands;

use App\Concerns\HasProcess;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{

    public function abort($message)
    {
        $this->error($message);die;
    }

    public function getWorkingDirectory()
    {
        return getcwd();
    }

}
