<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Y7K\Cli\Command;
use Y7K\Cli\Util;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{

    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Creates a new project')
            ->addArgument('path', InputArgument::OPTIONAL, 'Directory to install into')
            ->addOption('platform', 'p', InputOption::VALUE_OPTIONAL, 'Set to decide, which type to install (craft, laravel)')
//            ->addOption('dev', null, InputOption::VALUE_NONE, 'Set to download the dev version from the develop branch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load Question Helper
        $helper = $this->getHelper('question');

        // Get the path to install to
        $path = $input->getArgument('path');

        // Ask for it, if none was provided as argument
        if(!$path) {
            $question = new Question('Please enter the name of the Project (pj01-name): ');
            $question->setValidator(function ($answer) {
                if (!$answer) {
                    throw new \RuntimeException('Please enter a valid path to install the project to.');
                }
                return $answer;
            });
            $path = $helper->ask($input, $output, $question);
        }

        // Get which package to install
        $platform = $input->getOption('platform');
        $platforms = ['craft', 'laravel'];

        if (!in_array($platform, $platforms)) {
            $question = new ChoiceQuestion(
                'Please select which type of application you\'re building (Defaults to Craft):',
                array('Craft', 'Laravel'),
                0
            );
            $question->setErrorMessage('Type %s is invalid.');
            $platform = $helper->ask($input, $output, $question);
        }

        $command = $this->getApplication()->find('install:' . strtolower($platform));
        $arguments = new ArrayInput([
            'path'    => $path,
        ]);

        $returnCode = $command->run($arguments, $output);
    }


}
