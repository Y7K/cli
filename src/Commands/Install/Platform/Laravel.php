<?php

namespace Y7K\Cli\Commands\Install\Platform;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Y7K\Cli\Util;

class Laravel extends Command
{

    protected function configure()
    {
        $this->setName('install:laravel')
            ->setDescription('⏳  Install the Laravel Framework')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where u wanna put it, bro?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Get Paths
        $path = $input->getArgument('path');
        $filepath = $this->dir() . ($path ? '/' . $path : '');

        $this->install([
            'repo' => 'laravel/laravel',
            'branch' => 'master',
            'path' => $filepath,
            'output' => $output,
            'exclude' => ['resources/assets', 'public/css', 'public/js'],
            'success' => 'The Laravel Boilerplate is installed!',
        ]);

        $this->install([
            'repo' => 'y7k/plate',
            'branch' => 'master',
            'path' => $filepath,
            'output' => $output,
            'subfolders' => ['base', 'platforms/laravel'],
            'exclude' => ['base/.gitignore'],
            'success' => 'Laravel is installed! Yay!',
            'checkPath' => false
        ]);

        Util::findAndReplaceInFile($filepath . '/.env.example', '{name}', $path);
        Util::findAndReplaceInFile($filepath . '/composer.json', 'laravel/laravel', $path);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install',
            'run-script post-create-project-cmd'
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}
