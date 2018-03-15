<?php

namespace App\Concerns;


trait InteractsWithProjectJsonFile
{

    private $projectJsonFilename = 'project.json';
    protected $projectJsonData;

    public function readProjectJsonData()
    {
        $projectJsonFile = getcwd() . '/' . $this->projectJsonFilename;

        if (!file_exists($projectJsonFile)) {
            $this->abort("No {$this->projectJsonFilename} found!");
        }

        // Read  file contents
        $this->projectJsonData = json_decode(file_get_contents($projectJsonFile));
    }

    public function writeProjectJsonData()
    {
        $projectJsonFile = getcwd() . '/' . $this->projectJsonFilename;

        file_put_contents($projectJsonFile, json_encode($this->projectJsonData, JSON_PRETTY_PRINT));
    }
}