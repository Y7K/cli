<?php
namespace App\Commands\Content;

use App\Commands\BaseCommand;
use App\Concerns\ReadsY7KCliConfigFile;

abstract class BaseContentCommand extends BaseCommand
{

    use ReadsY7KCliConfigFile;

}
