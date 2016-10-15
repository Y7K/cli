<?php

namespace Y7K\Cli\Commands\Install;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Craft extends Command
{

    protected function configure()
    {
        $this->setName('install:craft')
            ->setDescription('Installs the Craft CMS plate')
            ->addArgument('path', InputArgument::REQUIRED, 'Directory to install into')//         ->addOption('dev', null, InputOption::VALUE_NONE, 'Set to download the dev version from the develop branch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->install([
            'repo' => 'y7k/plate',
            'branch' => 'develop',
            'path' => $this->dir() . '/' . $input->getArgument('path'),
            'output' => $output,
            'subfolders' => ['base', 'platforms/craft'],
            'success' => 'The craft code is installed!',
        ]);

        $output->writeln('Loading Craft CMS App folder...');

    }

}
