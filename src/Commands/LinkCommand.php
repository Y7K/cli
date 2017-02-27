<?php

namespace Y7K\Cli\Commands;

use Dotenv\Dotenv;
use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LinkCommand extends Command
{

    protected function configure()
    {
        $this->setName('storage:link')
            ->setDescription('Create a symbolic link from "public/storage" to "storage/app/public"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (file_exists('./public/storage')) {
            throw new RuntimeException('The "public/storage" directory already exists.');
        }

        symlink($this->dir() . '/storage/app/public', $this->dir() . '/public/storage');

        $output->writeln('The [public/storage] directory has been linked.');

    }

}
