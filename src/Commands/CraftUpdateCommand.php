<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CraftUpdateCommand extends Command
{

    protected function configure()
    {
        $this->setName('craft:update')
            ->setDescription('Update Craft to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $craftAppDir = $this->dir() . '/craft/app';

//        if (!is_dir($craftAppDir)) {
//            throw new RuntimeException('No craft/app folder found!');
//        }

        $output->writeln('Deleting /craft/app folder...');

        $process = new Process('rm -rf ' . $craftAppDir);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $this->install([
            'url' => 'http://craftcms.com/latest.zip?accept_license=yes',
            'path' => $craftAppDir,
            'output' => $output,
            'subfolders' => ['craft/app'],
            'success' => 'The craft app folder was updated!',
        ]);

    }

}
