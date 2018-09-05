<?php

namespace App\Concerns;


use stdClass;

trait WritesToJsonFile
{

    public function readJsonData($jsonFilename, $fileMustExist = true)
    {
        $jsonFile = $this->getWorkingDirectory() . '/' . $jsonFilename;

        if (!file_exists($jsonFile)) {
            return $fileMustExist ? $this->abort("No {$jsonFilename} found!") : new stdClass();
        }

        // Read  file contents
        return json_decode(file_get_contents($jsonFile));
    }

    public function writeJsonDataToFile($jsonFilename, $jsonData): void
    {
        $this->task("Update {$jsonFilename} file", function () use ($jsonFilename, $jsonData) {
            $jsonFile = $this->getWorkingDirectory() . '/' . $jsonFilename;
            if (file_exists($jsonFile)) {
                file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            }
        });
    }
}
