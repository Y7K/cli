<?php

namespace App\Concerns;

use App\Helpers\FileDownload;

trait InstallsRepository
{

    public function installRepositoryFromLocalSource($sourcePath, $destPath, $subfoldersToExtract)
    {

    }

    public function installRepositoryFromGitHub($githubRepository, $destPath, $subfoldersToExtract)
    {
        $url = 'https://api.github.com/repos/' . $githubRepository . '/zipball/master';
        $bar = $this->output->createProgressBar(100);

        $contents = FileDownload::download($url, $bar, env('GITHUB_USER') . ":" . env('GITHUB_TOKEN'));

        // write the result to the disk
//        file_put_contents('tmp.zip', $contents);
    }

    public function installRepositoryFromUrl($url, $destPath, $subfoldersToExtract)
    {

    }

}
