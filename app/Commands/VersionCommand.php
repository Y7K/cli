<?php
namespace App\Commands;


use App\Concerns\WritesToJsonFile;

class VersionCommand extends BaseCommand
{

    use WritesToJsonFile;

    protected $signature = 'version';
    protected $description = '#⃣  Get the Project Version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $projectJsonData = $this->readJsonData('project.json');

        if (!isset($projectJsonData->version)) {
            $this->abort('No version specified in project.json file!');
        }

        $this->info($projectJsonData->version);
    }

}
