<?php

namespace Y7K\Cli\Commands\Content;

use RuntimeException;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
use Y7K\Cli\Command;
use Y7K\Cli\Util;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ContentPullCommand extends Command
{

    protected function configure()
    {
        $this->setName('content:pull')
            ->setDescription('⬇  Pulls DB & assets from a specific environment to local')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment name (defined in .y7k-cli.json)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Answers all security questions with yes automatically');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $environment = strtolower($input->getArgument('environment'));

        // Run db:pull command
        $dbPullCommand = $this->getApplication()->find('db:pull');
        $arguments = new ArrayInput([
            'environment'    => $environment,
            '-f'    => $input->getOption('force'),
        ]);

        $dbPullCommand->run($arguments, $output);

        // Run assets:pull command
        $assetsPullCommand = $this->getApplication()->find('assets:pull');
        $arguments = new ArrayInput([
            'environment'    => $environment,
            '-f'    => $input->getOption('force'),
        ]);
        $assetsPullCommand->run($arguments, $output);

    }


}
