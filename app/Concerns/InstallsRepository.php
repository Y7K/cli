<?php

namespace App\Concerns;

use App\Helpers\FileHelper;

trait InstallsRepository
{

    public function installRepositoryFromLocalSource($sourcePath, $destPath, $subfoldersToExtract)
    {


    }

    public function installRepositoryFromGitHub($githubRepository, $destinationPath, $subfoldersToExtract)
    {
        $url = 'https://api.github.com/repos/' . $githubRepository . '/zipball/master';


        // Download Repository as Zip
        $this->info("Downloading {$githubRepository} Repository from GitHub");
        $tempZipFile = FileHelper::downloadToZip($url,  $this->output, env('GITHUB_USER') . ":" . env('GITHUB_TOKEN'));

        // Unzip content
        $this->info("");
        $this->info("Unzipping files");
        $tempFolder = FileHelper::unzip($tempZipFile, true);

        var_dump($tempFolder);

    }

    public function installRepositoryFromUrl($url, $destPath, $subfoldersToExtract)
    {

    }

    private function getTempZipFileName()
    {

    }

}
