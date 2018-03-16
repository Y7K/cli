<?php

namespace App\Concerns;

use App\Helpers\FileHelper;

trait InstallsRepository
{

    public function installRepositoryFromLocalSource($sourcePath, $destPath, $subfoldersToExtract)
    {


    }

    public function installRepositoryFromGitHub($githubRepository, $options)
    {
        $url = 'https://api.github.com/repos/' . $githubRepository . '/zipball/master';

        // Download Repository as Zip
        $this->info("Downloading {$githubRepository} Repository from GitHub...");
        $tempZipFile = FileHelper::downloadToZip($url,  $this->output, env('GITHUB_USER') . ":" . env('GITHUB_TOKEN'));
        $this->info("");

        // Unzip content
        $this->info("Unzipping files...");
        $tempFolder = FileHelper::unzip($tempZipFile, true);

        // Copy Files to Destination
        $this->info("Copying files to destination...");
        FileHelper::extractFilesToDirectory($tempFolder, $options);

        $this->info("");
        $this->info("Repository {$githubRepository} has been installed!");
    }

    public function installRepositoryFromUrl($url, $destPath, $subfoldersToExtract)
    {

    }


}
