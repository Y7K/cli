<?php

namespace App\Commands;

use App\Concerns\HasProcess;
use App\Concerns\InteractsWithGit;
use App\Concerns\WritesToJsonFile;

class BumpCommand extends BaseCommand
{

    use WritesToJsonFile, InteractsWithGit, HasProcess;

    protected $signature = 'bump {version? : Major, minor or patch} {--g|nogit}';
    protected $description = '🚠  Bump the Project Version';

    protected $projectJsonData;
    protected $composerJsonData;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->readProjectVersionAndIncreaseIt();

        $this->task("Update version to <fg=green>{$this->projectJsonData->version}</>", function () {

            if ($this->option('nogit')) {
                $this->writeVersionToFiles();
            } else {
                $this->abortIfThereAreUncommitedFiles();
                $this->abortIfNewCommitsAreAvailableToPull();
                $this->writeProjectJsonAndCommit();
            }

        });

    }

    public function readProjectVersionAndIncreaseIt()
    {

        $this->projectJsonData = $this->readJsonData('project.json');
        $this->composerJsonData = $this->readJsonData('composer.json', false);

        if (!isset($this->projectJsonData->version)) {
            $this->abort('No version specified in project.json file!');
        }

        // Get the Version to Update
        $version = strtolower($this->argument('version'));
        $possibleAnswers = ['major', 'minor', 'patch'];

        if (!\in_array($version, $possibleAnswers, true)) {
            $version = $this->choice('Which number do you want to increase?', $possibleAnswers, count($possibleAnswers) - 1);
        }

        // Extract Version to Array
        $projectVersion = explode('.', $this->projectJsonData->version);
        $versionUpdate = array_search($version, $possibleAnswers, true);

        // Increase selected Number
        $projectVersion[$versionUpdate] = (int)$projectVersion[$versionUpdate] + 1;

        // Set Trailing numbers to Zero
        if ($versionUpdate < \count($projectVersion) - 1) {
            foreach (range($versionUpdate + 1, \count($projectVersion) - 1) as $v) {
                $projectVersion[$v] = 0;
            }
        }

        // Save the updated Versin
        $this->projectJsonData->version = implode('.', $projectVersion);
        $this->composerJsonData->version = implode('.', $projectVersion);

        return $this;
    }

    public function writeProjectJsonAndCommit(): void
    {
        $projectVersionString = $this->projectJsonData->version;

        if ($this->isUsingGitFlow()) {

            // Checkout Release Branch

            $this->task('Create release branch', function () use ($projectVersionString) {
                $this->runProcess("git checkout develop && git checkout -b release/{$projectVersionString} develop", true);
            });

            // Write File
            $this->writeVersionToFiles();

            // Commit changes, merge and tag release branch
            $this->task('Create tag and merge release branch', function () use ($projectVersionString) {
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
                ], true);
            });

        } else {

            $this->writeVersionToFiles();

            $this->task('Create tag and commit', function () use ($projectVersionString) {
                $this->runProcessSequence([
                    "git add --all",
                    "git commit -m \"Release {$projectVersionString}\"",
                    "git tag -a {$projectVersionString} -m \"{$projectVersionString}\""
                ]);
            });

        }

    }

    public function writeVersionToFiles(): void
    {
        $this->writeJsonDataToFile('project.json', $this->projectJsonData);
        $this->writeJsonDataToFile('composer.json', $this->composerJsonData);
    }

}
