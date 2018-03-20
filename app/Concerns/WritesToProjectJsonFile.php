<?php

namespace App\Concerns;


trait WritesToProjectJsonFile
{

    private $projectJsonFilename = 'project.json';
    protected $projectJsonData;

    public function readProjectJsonData()
    {
        $projectJsonFile = $this->getWorkingDirectory() . '/' . $this->projectJsonFilename;

        if (!file_exists($projectJsonFile)) {
            $this->abort("No {$this->projectJsonFilename} found!");
        }

        // Read  file contents
        $this->projectJsonData = json_decode(file_get_contents($projectJsonFile));
    }

    public function writeProjectJsonDataToFile()
    {
        $this->task("Update project.json file", function () {
            $projectJsonFile = $this->getWorkingDirectory() . '/' . $this->projectJsonFilename;
            file_put_contents($projectJsonFile, json_encode($this->projectJsonData, JSON_PRETTY_PRINT));
        });
    }
}
