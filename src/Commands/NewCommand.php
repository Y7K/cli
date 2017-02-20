<?php

namespace Y7K\Cli\Commands;

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

class NewCommand extends Command
{

    protected function configure()
    {
        $this->setName('new')
            ->setDescription('👻  Install a shiny new Project')
            ->addArgument('path', InputArgument::OPTIONAL, 'Choose a folder, I\'ll take care of the rest.')
            ->addOption('platform', 'p', InputOption::VALUE_OPTIONAL, 'Which Type shall it be: Craft, Laravel or a static Site?')
            ->addOption('javascript', 'j', InputOption::VALUE_OPTIONAL, 'Which JavaScript Boilerplate do you need?')
            ->addOption('stylesheets', 'c', InputOption::VALUE_OPTIONAL, 'Which SCSS Boilerplate do you need?')
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
            $question = new Question('Set a Name for the Project (eg. pj01-name):' . PHP_EOL);
            $question->setValidator(function ($answer) {
                if (!$answer) {
                    throw new \RuntimeException('Please enter a valid path to install the project to.');
                }
                return $answer;
            });
            $path = $helper->ask($input, $output, $question);
        }

        $output->writeln('');
        $output->writeln('Name set to <info>' . $path . '</info>.');
        $output->writeln('');


//        Platform

        // Get which package to install
        $platform = $input->getOption('platform');
        $platforms = ['craft', 'laravel', 'plain'];

        if (!in_array($platform, $platforms)) {
            $question = new ChoiceQuestion(
                'Please select which type of application you\'re building (Defaults to <info>Craft</info>):',
                array('Craft', 'Laravel', 'Plain'),
                0
            );
            $question->setErrorMessage('Type %s is invalid.');
            $platform = $helper->ask($input, $output, $question);
        }

        $output->writeln('');
        $output->writeln('Installing the <info>' . ucfirst($platform) . '</info> Package...');
        $output->writeln('');

        $platformCommand = $this->getApplication()->find('install:' . str_replace(' ', '-', strtolower($platform)));
        $platformArguments = new ArrayInput([
            'path'    => $path,
        ]);

        $returnCode = $platformCommand->run($platformArguments, $output);


        $output->writeln('');

        // Javascript
        $jsCommand = $this->getApplication()->find('install:javascript');
        $jsArguments = new ArrayInput([
            'path'    => $path,
        ]);

        $returnCode = $jsCommand->run($jsArguments, $output);


        $output->writeln('');

        // SCSS
        $jsCommand = $this->getApplication()->find('install:stylesheets');
        $scssArguments = new ArrayInput([
            'path'    => $path,
        ]);

        $returnCode = $jsCommand->run($scssArguments, $output);


        Util::findAndReplaceInFile($this->dir() . '/' . $path . '/package.json', '{name}', $path);

        $process = new Process('cd ./' .$path .' && git init && git flow init && git add --all && git commit -m "⚡️️ Initial Commit"');
        $process->setTimeout(120 * 3600);
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

    }


}
