<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BumpCommand extends Command
{

    protected function configure()
    {
        $this->setName('bump')
            ->setDescription('Bump the Project Version')
            ->addArgument('version', InputArgument::REQUIRED, 'Major, Minor or Patch')
            ->addOption('nogit', 'g', InputOption::VALUE_NONE, 'Should only the version be updated and no git actions be done?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Check if Porjec.json file exists
        $projectFile = $this->dir() . '/project.json';

        if (!file_exists($projectFile)) {
            throw new RuntimeException('Project.json File not found!');
        }

        // Read project.json file
        $projectData = json_decode(file_get_contents($projectFile));

        if (!isset($projectData->version)) {
            throw new RuntimeException('No version specified in project.json file!');
        }

        // Get the version to update
        $version = strtolower($input->getArgument('version'));
        $semver = ['major', 'minor', 'patch'];

        if(!in_array($version, $semver)) {
            throw new RuntimeException('Version Argument must be \'major\', \'minor\' or \'patch\'');
        }

        // Extract Version to Array
        $projectVersion = explode('.',$projectData->version);
        $update = array_search($version, $semver);

        // Increase selected Number
        $projectVersion[$update] = (int) $projectVersion[$update] + 1;

        // Set Trailing numbers to Zero
        if($update<count($projectVersion) - 1) {
            foreach (range($update + 1, count($projectVersion) - 1) as $v) {
                $projectVersion[$v] = 0;
            }
        }

        // Save the updated Versin
        $projectData->version = implode('.', $projectVersion);
        $projectVersionString = $projectData->version;

        exec('git branch --list develop', $res1);
        exec('git branch --list master', $res2);
        exec('git rev-parse --abbrev-ref HEAD', $res3);
        $hasDevBranch = count($res1) > 0;
        $hasMasterBranch = count($res2) > 0;
        $currentBranch = $res3[0];


        // If "nogit", skip the whole git stuff
        $noGit = $input->getOption('nogit');
        if(!$notGit) {

            // If there is a "develop" and "master branch" (meaning, we're probably using git-flow), we do the following:
            //
            // 1. Create new release branch
            // 2. Update the project file
            // 3. Commit the change to the release branch
            // 4. Merge the release branch into master
            // 5. Merge the release branch into develop
            if($hasDevBranch && $hasMasterBranch) {
                $process = new Process('git checkout develop && git checkout -b release/' . $projectVersionString .' develop');
                $process->run();

                file_put_contents($projectFile, json_encode($projectData, JSON_PRETTY_PRINT));

                $process = new Process('export GIT_MERGE_AUTOEDIT=no && ' .
                    'git add --all && git commit -m "Release ' . $projectVersionString . '" && ' .
                    'git checkout master && git merge release/' . $projectVersionString . ' && git tag -a ' . $projectVersionString . ' -m "' . $projectVersionString . '" &&' .
                    'git checkout develop && git merge release/' . $projectVersionString . ' && git branch -d release/' . $projectVersionString . ' &&' .
                    'unset GIT_MERGE_AUTOEDIT');
            } else {

                // If there is not "master" and "develop", we simply update the file and commit to the current branch

                // Write To file
                file_put_contents($projectFile, json_encode($projectData, JSON_PRETTY_PRINT));

                $process = new Process('git add --all && git commit -m "Release ' . $projectVersionString . '" && ' .
                                       'git tag -a ' . $projectVersionString . ' -m "' . $projectVersionString . '"');
            }

            if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
                $process->setTty(true);
            }

            // Run git processes
            $process->run(function ($type, $line) use ($output) {
                $output->write($line);
            });

        } else {
            // If no git, simply update project file
            file_put_contents($projectFile, json_encode($projectData, JSON_PRETTY_PRINT));
        }


        $output->writeln('<info>Version updated to ' . $projectVersionString . '</info>');
    }

}
