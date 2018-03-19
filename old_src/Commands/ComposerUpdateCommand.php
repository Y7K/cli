<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerUpdateCommand extends Command
{

    protected function configure()
    {
        $this->setName('composer:update')
            ->setDescription('🔃  Run composer update and commit the file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('Execute composer update');


        $filepath = $this->dir();

        $commands = [
            'update --no-scripts',
        ];

        $this->runComposerCommands($input, $output, $filepath, $commands);

        $process = new Process('git add composer.lock && git commit -m "Ran Composer Update"');
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });


        $output->writeln('<comment>' .'Composer updated successfully.</comment>');

    }

}
