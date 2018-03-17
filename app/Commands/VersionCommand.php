<?php
namespace App\Commands;


use App\Concerns\WritesToProjectJsonFile;

class VersionCommand extends BaseCommand
{

    use WritesToProjectJsonFile;

    protected $signature = 'version';
    protected $description = '#⃣  Get the Project Version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->readProjectJsonData();

        if (!isset($this->projectJsonData->version)) {
            $this->abort('No version specified in project.json file!');
        }

        $this->info($this->projectJsonData->version);
    }

}
