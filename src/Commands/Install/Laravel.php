<?php

namespace Y7K\Cli\Commands\Install;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Laravel extends Command
{

    protected function configure()
    {
        $this->setName('install:laravel')
            ->setDescription('Installs Laravel')
            ->addArgument('path', InputArgument::REQUIRED, 'Directory to install into')
            //         ->addOption('dev', null, InputOption::VALUE_NONE, 'Set to download the dev version from the develop branch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $path = $input->getArgument('path');

        $this->install([
            'repo' => 'laravel/laravel',
            'branch' => 'master',
            'path' => $this->dir() . '/' . $path,
            'output' => $output,
            'exclude' => ['gulpfile.js', 'resources/assets'],
            'success' => 'The Laravel is installed!',
        ]);

        $this->install([
            'repo' => 'y7k/plate',
            'branch' => 'develop',
            'path' => $this->dir() . '/' . $path,
            'output' => $output,
            'subfolders' => ['base', 'platforms/laravel'],
            'exclude' => ['base/composer.json', 'base/composer.lock', 'base/.gitignore'],
            'success' => 'The laravel boilerplate is installed!',
            'checkPath' => false
        ]);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install',
            'run-script post-install-cmd',
            'run-script post-create-project-cmd'
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}
