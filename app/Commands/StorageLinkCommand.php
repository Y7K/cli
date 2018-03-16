<?php

namespace App\Commands;

class StorageLinkCommand extends BaseCommand
{

    protected $signature = 'storage:link';
    protected $description = '📦  Create a symbolic link from "public/storage" to "storage/app/public"';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (file_exists($this->getWorkingDirectory() . '/public/storage')) {
            $this->abort("The [public/storage] directory already exists.");
        }

        if (!file_exists($this->getWorkingDirectory() . '/storage/app/public')) {
            $this->abort("The [storage/app/public] directory does not exist!");
        }

        if (!file_exists($this->getWorkingDirectory() . '/public')) {
            $this->abort("The [public] directory does not exist!");
        }

        symlink('../storage/app/public', $this->getWorkingDirectory() . '/public/storage');

        $this->info('The [public/storage] directory has been linked.');
    }


}
