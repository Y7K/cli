<?php

namespace App\Concerns;

use App\Helpers\FileHelper;

trait InstallsRepository
{

    public function installY7KRepo($repoName, $options, $local){
        $installRepositorycommand = ($local) ? 'installRepositoryFromLocalSource' : 'installRepositoryFromGitHub';
        $this->{$installRepositorycommand}('y7k/' . $repoName, $options);
    }

    public function installRepositoryFromLocalSource($githubRepository, $options)
    {
        $this->task("Loading {$githubRepository} repository from local source", function () use ($githubRepository, $options) {
            $repositoryPath = $this->getLocalRepositoryPath($githubRepository);
            $this->copyFilesToDestination($repositoryPath, $options);
        });
    }


    public function installRepositoryFromGitHub($githubRepository, $options, $branch = 'master')
    {
        $this->task("Downloading {$githubRepository} repository from GitHub", function () use ($githubRepository, $options, $branch) {
            $url = $this->generateZipballUrl($githubRepository, $branch);
            $this->downloadAndExtractFiles($url, $options, true);
        });
    }


    public function installRepositoryFromUrl($url, $options)
    {
        $this->task("Downloading files from the internets", function () use ($url, $options) {
            $this->downloadAndExtractFiles($url, $options);
        });
    }


    private function downloadAndExtractFiles($url, $options, $downloadFromGitHub = false)
    {
        $auth = ($downloadFromGitHub) ? $this->getGitHubAuth() : false;

        // Download Repository as Zip
        $tempZipFile = FileHelper::downloadToZip($url,  $this->output, $auth);

        // Unzip content
        $this->info("");
        $this->info("Unzipping files...");
        $tempFolder = FileHelper::unzip($tempZipFile, $downloadFromGitHub);

         $this->copyFilesToDestination($tempFolder, $options);

        // Remove temp folder
        FileHelper::deleteDirectory($tempFolder);
    }

    private function copyFilesToDestination($folder, $options)
    {
        // Copy Files to Destination
        $this->task("Copying files to destination", function () use ($folder, $options) {
            FileHelper::copyFilesToDirectory($folder, $options);
        });
    }


}
