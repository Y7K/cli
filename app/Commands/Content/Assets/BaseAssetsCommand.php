<?php

namespace App\Commands\Content\Assets;


use App\Commands\Content\BaseContentCommand;

abstract class BaseAssetsCommand extends BaseContentCommand
{

    public function buildRsyncCommand($source, $destination)
    {
        $source = rtrim($source, '/');
        $destination = rtrim($destination, '/');
        return "rsync -avz --delete-excluded --include=\".git*\" --exclude=\".*\" {$source}/ {$destination}";
    }


}
