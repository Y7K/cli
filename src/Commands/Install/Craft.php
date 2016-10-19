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
            ->setDescription('Install Craft CMS')
            ->addArgument('path', InputArgument::REQUIRED, 'Directory to install into')//         ->addOption('dev', null, InputOption::VALUE_NONE, 'Set to download the dev version from the develop branch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $path = $input->getArgument('path');

        $this->install([
            'repo' => 'y7k/plate',
            'branch' => 'develop',
            'path' => $this->dir() . '/' . $path,
            'output' => $output,
            'subfolders' => ['base', 'platforms/craft'],
            'success' => 'The craft boilerplate is installed!',
        ]);

        $this->install([
            'url' => 'http://craftcms.com/latest.zip?accept_license=yes',
            'path' => $this->dir() . '/' . $path . '/craft/app',
            'output' => $output,
            'subfolders' => ['craft/app'],
            'success' => 'The craft app folder is installed!',
        ]);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install'
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}
