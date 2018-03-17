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

        foreach ([
                     'public/storage' => false,
                     'storage/app/public' => true,
                     'public' => true,
                 ] as $directory => $shouldExist) {
            if (file_exists($this->getWorkingDirectory() . '/' . $directory) !== $shouldExist) {
                $this->abort(
                    ($shouldExist)
                        ? "The [{$directory}] directory does not exist."
                        : "The [{$directory}] directory already exists."
                );
            }
        }


        symlink('../storage/app/public', $this->getWorkingDirectory() . '/public/storage');

        $this->info('The [public/storage] directory has been linked.');
    }


}
