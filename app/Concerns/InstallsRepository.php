<?php

namespace App\Concerns;

use App\Helpers\FileHelper;

trait InstallsRepository
{

    public function installRepositoryFromLocalSource($sourcePath, $destPath, $subfoldersToExtract)
    {


    }

    public function installRepositoryFromGitHub($githubRepository, $options, $branch = 'master')
    {
        $this->info("Downloading {$githubRepository} repository from GitHub...");

        $url = "https://api.github.com/repos/{$githubRepository}/zipball/{$branch}";
        $this->downloadAndExtractFiles($url, $options, true);
    }

    public function installRepositoryFromUrl($url, $options)
    {
        $this->info("Downloading files from the internets...");

        $this->downloadAndExtractFiles($url, $options);
    }

    private function downloadAndExtractFiles($url, $options, $downloadFromGitHub = false)
    {
        $auth = ($downloadFromGitHub) ? env('GITHUB_USER') . ":" . env('GITHUB_TOKEN') : false;

        // Download Repository as Zip
        $tempZipFile = FileHelper::downloadToZip($url,  $this->output, $auth);
        $this->info("");

        // Unzip content
        $this->info("Unzipping files...");
        $tempFolder = FileHelper::unzip($tempZipFile, $downloadFromGitHub);

        // Copy Files to Destination
        $this->info("Copying files to destination...");
        FileHelper::extractFilesToDirectory($tempFolder, $options);

        $this->info("");
        $this->info("Package has been installed!");
    }


}
