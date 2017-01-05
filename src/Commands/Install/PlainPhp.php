<?php

namespace Y7K\Cli\Commands\Install;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PlainPhp extends Command
{

    protected function configure()
    {
        $this->setName('install:plain-php')
            ->setDescription('Install Plain PHP File Boilerplate')
            ->addArgument('path', InputArgument::REQUIRED, 'Directory of your choosing. Where the stuff will end up.')
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
            'subfolders' => ['1-base', '2-platforms/plain-php'],
            'success' => 'The Boilerplate code was loaded and installed!',
            'checkPath' => false
        ]);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install',
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}
