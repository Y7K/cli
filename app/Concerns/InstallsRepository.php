<?php

namespace App\Concerns;

use App\Helpers\FileHelper;

trait InstallsRepository
{

    public function installY7KRepo($repoName, $options, $remote){
        $installRepositorycommand = ($remote) ? 'installRepositoryFromGitHub' : 'installRepositoryFromLocalSource';
        $this->{$installRepositorycommand}('y7k/' . $repoName, $options);
    }

    public function installRepositoryFromLocalSource($githubRepository, $options)
    {
        $this->info("Loading {$githubRepository} repository from local source...");

        $repositoryPath = $this->getLocalRepositoryPath($githubRepository);

        $this->copyFilesToDestination($repositoryPath,  $options);

        $this->info("Package {$githubRepository} has been installed from local source!");
    }


    public function installRepositoryFromGitHub($githubRepository, $options, $branch = 'master')
    {
        $this->info("Downloading {$githubRepository} repository from GitHub...");

        $url = $this->generateZipballUrl($githubRepository, $branch);
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
        $auth = ($downloadFromGitHub) ? $this->getGitHubAuth() : false;

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
