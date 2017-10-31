<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VersionCommand extends Command
{

    protected function configure()
    {
        $this->setName('version')
            ->setDescription('#⃣  Get the Project Version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $projectFile = $this->dir() . '/project.json';

        if (!file_exists($projectFile)) {
            throw new RuntimeException('Project.json File not found!');
        }

        $projectData = json_decode(file_get_contents($projectFile));

        if (!isset($projectData->version)) {
            throw new RuntimeException('No version specified in project.json file!');
        }

        $output->writeln($projectData->version);

    }

}
