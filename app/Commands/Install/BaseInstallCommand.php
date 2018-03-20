<?php
namespace App\Commands\Install;


use App\Commands\BaseCommand;
use App\Concerns\HasProcess;
use App\Concerns\InstallsRepository;
use App\Concerns\InteractsWithGitHubApi;
use App\Concerns\LoadsDataFromLocalRepository;

abstract class BaseInstallCommand extends BaseCommand
{
    use InstallsRepository, HasProcess, InteractsWithGitHubApi, LoadsDataFromLocalRepository;

    public function abortIfDirectoryExists($destinationPath, $createDir = true)
    {
        if (is_dir($destinationPath) || ($createDir && !mkdir($destinationPath) && !is_dir($destinationPath))) {
            $this->abort("Directory {$destinationPath} already exists or could not be created");
        }
    }

    public function runPostInstallComposerCommands($destinationPath)
    {
        // Run Composer Commands
        $this->task("Run Composer scripts", function () use ($destinationPath) {

            $commands = [
                "cd {$destinationPath}",
                "composer install --no-scripts",
                "composer run-script post-root-package-install",
                "composer run-script post-create-project-cmd",
            ];

            $this->runProcessSequence($commands, true);
        });
    }

//    public function customizeEnvAndComposerFile($destinationPath)
//    {
//        $explodedPath = explode('-', $destinationPath,2);
//        $projectCode = $explodedPath[0];
//        $projectName = ucwords(str_replace('-', ' ', $explodedPath[count($explodedPath) - 1]));
//        $repoName = "y7k/" . $destinationPath;
//    }


}
