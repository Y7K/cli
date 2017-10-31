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

class ContentPushCommand extends Command
{

    protected function configure()
    {
        $this->setName('content:push')
            ->setDescription('⬆  Pushes DB & assets from local to a specific environment')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment name (defined in .y7k-cli.json)');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $environment = strtolower($input->getArgument('environment'));

        // Run db:push command
        $dbPushCommand = $this->getApplication()->find('db:push');
        $arguments = new ArrayInput([
            'environment'    => $environment,
        ]);
        $dbPushCommand->run($arguments, $output);

        // Run assets:push command
        $assetsPushCommand = $this->getApplication()->find('assets:push');
        $arguments = new ArrayInput([
            'environment'    => $environment,
        ]);
        $assetsPushCommand->run($arguments, $output);

    }


}
