<?php

namespace Y7K\Cli\Commands\Install\Platform;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Y7K\Cli\Util;

class Plain extends Command
{

    protected function configure()
    {
        $this->setName('install:plain')
            ->setDescription('⏳  Install Plain Boilerplate')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where is the output folder?')
            ->addOption('remote', 'r', InputOption::VALUE_NONE, 'Load Plate from online repository instead from local?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Get Paths
        $path = $input->getArgument('path');
        $filepath = $this->dir() . ($path ? '/' . $path : '');

        $output->writeln('');
        $output->writeln('Installing the <info>Plain PHP</info> Boilerplate...');
        $output->writeln('');

        if($input->getOption('remote')) {
            $this->installFromRemote([
                'repo' => 'y7k/plate',
                'branch' => 'master',
                'path' => $filepath,
                'output' => $output,
                'subfolders' => ['base', 'platforms/plain'],
                'success' => 'The Plain PHP Boilerplate has been loaded from remote!',
                'checkPath' => false
            ]);
        } else {
            $this->installFromLocal([
                'sourcePath' => getenv('PATH_PLATE'),
                'subfolders' => ['base', 'platforms/plain'],
                'destPath' => $filepath,
                'output' => $output,
                'success' => 'The Plain PHP Boilerplate has been loaded from local!',
            ]);
        }

//        Util::findAndReplaceInFile($filepath . '/.env.example', '{name}', $path);
//        Util::findAndReplaceInFile($filepath . '/composer.json', '{name}', $path);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install',
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}
