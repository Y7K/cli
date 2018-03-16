<?php

namespace App\Concerns;

use App\Helpers\FileHelper;

trait InstallsRepository
{

    public function installRepositoryFromLocalSource($githubRepository, $options)
    {

        $availableRepositories = [
           'y7k/plate' => 'PATH_PLATE',
           'y7k/scripts' => 'PATH_SCRIPTS',
           'y7k/style' => 'PATH_STYLE',
           'y7k/components' => 'PATH_COMPONENTS',
        ];

        if(!array_key_exists($githubRepository, $availableRepositories)) {
            $this->abort("Tried to install unkown repository from local source: {$githubRepository}.");
        }

        $this->copyFilesToDestination(env($availableRepositories[$githubRepository]), $options);

        $this->info("Package {$githubRepository} has been installed from local source!");
    }


    public function installRepositoryFromGitHub($githubRepository, $options, $branch = 'master')
    {
        $this->info("Downloading {$githubRepository} repository from GitHub...");

        $url = "https://api.github.com/repos/{$githubRepository}/zipball/{$branch}";
        $this->downloadAndExtractFiles($url, $options, true);

        $this->info("Package {$githubRepository} has been installed from GitHub!");
    }


    public function installRepositoryFromUrl($url, $options)
    {
        $this->info("Downloading files from the internets...");

        $this->downloadAndExtractFiles($url, $options);

        $this->info("Package has been installed!");
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

        $this->copyFilesToDestination($tempFolder, $options);

        // Remove temp folder
        FileHelper::deleteDirectory($tempFolder);
    }

    private function copyFilesToDestination($folder, $options)
    {
        // Copy Files to Destination
        $this->info("Copying files to destination...");
        FileHelper::copyFilesToDirectory($folder, $options);
    }


}
