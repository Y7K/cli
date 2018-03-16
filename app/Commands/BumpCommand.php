<?php

namespace App\Commands;

use App\Concerns\HasProcess;
use App\Concerns\InteractsWithGit;
use App\Concerns\WritesToProjectJsonFile;

class BumpCommand extends BaseCommand
{

    use WritesToProjectJsonFile, InteractsWithGit, HasProcess;

    protected $signature = 'bump {version? : Major, minor or patch} {--g|nogit}';
    protected $description = '🚠  Bump the Project Version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->readAndUpdateProjectVersion();

        if ($this->option('nogit')) {
            $this->writeProjectJsonDataToFile();
        } else {
            $this->abortIfThereAreUncommitedFiles();
            $this->abortIfNewCommitsAreAvailableToPull();
            $this->writeProjectJsonAndCommit();
        }

        $this->info("Version updated to {$this->projectJsonData->version}!");
    }

    public function readAndUpdateProjectVersion()
    {

        $this->readProjectJsonData();

        if (!isset($this->projectJsonData->version)) {
            $this->abort('No version specified in project.json file!');
        }

        // Get the Version to Update
        $version = strtolower($this->argument('version'));
        $possibleAnswers = ['major', 'minor', 'patch'];

        if (!in_array($version, $possibleAnswers)) {
            $version = $this->choice('Which number do you want to increase?', $possibleAnswers, count($possibleAnswers) - 1);
        }

        // Extract Version to Array
        $projectVersion = explode('.', $this->projectJsonData->version);
        $versionUpdate = array_search($version, $possibleAnswers);

        // Increase selected Number
        $projectVersion[$versionUpdate] = (int)$projectVersion[$versionUpdate] + 1;

        // Set Trailing numbers to Zero
        if ($versionUpdate < count($projectVersion) - 1) {
            foreach (range($versionUpdate + 1, count($projectVersion) - 1) as $v) {
                $projectVersion[$v] = 0;
            }
        }

        // Save the updated Versin
        $this->projectJsonData->version = implode('.', $projectVersion);

        return $this;
    }

    public function writeProjectJsonAndCommit()
    {

        $projectVersionString = $this->projectJsonData->version;

        if ($this->isUsingGitFlow()) {

            // Checkout Release Branch
            $this->runProcess("git checkout develop && git checkout -b release/{$projectVersionString} develop");

            // Write File
            $this->writeProjectJsonDataToFile();

            // Commit changes, merge and tag release branch
            $this->runProcessSequence([
                "export GIT_MERGE_AUTOEDIT=no",
                "git add --all",
                "git commit -m \"Release {$projectVersionString}\"",
                "git checkout master",
                "git merge release/{$projectVersionString}",
                "git tag -a {$projectVersionString} -m \"{$projectVersionString}\"",
                "git checkout develop",
                "git merge release/{$projectVersionString}",
                "git branch -d release/{$projectVersionString}",
                "unset GIT_MERGE_AUTOEDIT"
            ]);

        } else {

            $this->writeProjectJsonDataToFile();

            $this->runProcessSequence([
                "git add --all",
                "git commit -m \"Release {$projectVersionString}\"",
                "git tag -a {$projectVersionString} -m \"{$projectVersionString}\""
            ]);

        }

    }

}
