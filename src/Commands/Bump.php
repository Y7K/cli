<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Bump extends Command
{

    protected function configure()
    {
        $this->setName('bump')
            ->setDescription('Bump the Project Version')
            ->addArgument('version', InputArgument::REQUIRED, 'Major, Minor or Patch');
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


        // Write To file
        file_put_contents($projectFile, json_encode($projectData, JSON_PRETTY_PRINT));

        $output->writeln('<info>Version updated to ' . $projectData->version . '</info>');
    }

}
