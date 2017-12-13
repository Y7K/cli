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
            ->setDescription('⏳  Install the Laravel Framework. Plus some Y7K Magic Sugar.')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where is the output folder?')
            ->addOption('remote', 'r', InputOption::VALUE_NONE, 'Load Plate from online repository instead from local?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Get Paths
        $path = $input->getArgument('path');
        $filepath = $this->dir() . ($path ? '/' . $path : '');

        $output->writeln('');
        $output->writeln('Installing the <info>Laravel</info> Boilerplate...');
        $output->writeln('');

        $this->installFromRemote([
            'repo' => 'laravel/laravel',
            'branch' => 'master',
            'path' => $filepath,
            'output' => $output,
            'exclude' => ['resources/assets', 'public/css', 'public/js'],
            'success' => 'The Laravel Framework (Vendor) has been loaded from remote!',
            'checkPath' => false
        ]);


        if($input->getOption('remote')) {
            $this->installFromRemote([
                'repo' => 'y7k/plate',
                'branch' => 'master',
                'path' => $filepath,
                'output' => $output,
                'subfolders' => ['base', 'platforms/laravel'],
                'exclude' => ['base/.gitignore'],
                'success' => 'The Laravel Boilerplate has been loaded from remote!',
                'checkPath' => false
            ]);
        } else {
            $this->installFromLocal([
                'sourcePath' => getenv('PATH_PLATE'),
                'subfolders' => ['base', 'platforms/laravel'],
                'destPath' => $filepath,
                'output' => $output,
                'success' => 'The Laravel Boilerplate has been loaded from local!',
            ]);
        }


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
