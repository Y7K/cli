<?php

namespace Y7K\Cli\Commands\Install\Resources;

use RuntimeException;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Y7K\Cli\Util;

class JavaScript extends Command
{

    protected function configure()
    {
        $this->setName('install:javascript')
            ->setDescription('Install JavaScript Boilerplate')
            ->addArgument('path', InputArgument::REQUIRED, 'Where does the Project live in?')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Which JavaScript setup do you need?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Load Question Helper
        $helper = $this->getHelper('question');


        $path = $input->getArgument('path');
        $filepath = $this->dir() . '/' . $path;
        $packageJson = $filepath. '/package.json';


        $originalPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : NULL;


        // Get which package to install
        $type = $input->getOption('type');
        $types = ['default'];

        if (!in_array($type, $types)) {
            $question = new ChoiceQuestion(
                'Please select which JavaScript Boilerplate you need (Defaults to <info>Default</info>):',
                array('Default'),
                0
            );
            $question->setErrorMessage('Type %s is invalid.');
            $type = $helper->ask($input, $output, $question);
        }

        $output->writeln('');
        $output->writeln('Installing the <info>' . ucfirst($type) . '</info> JS Boilerplate...');
        $output->writeln('');

        $folderName = explode(' ', strtolower($type))[0];


        $this->install([
            'repo' => 'y7k/plate',
            'branch' => 'develop',
            'path' => $filepath,
            'output' => $output,
            'subfolders' => ['3-js/' . $folderName],
            'success' => 'The JavaScript boilerplate has been loaded!',
            'checkPath' => false
        ]);


        $newPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : NULL;
        $mergedPackageJson = $this->mergeJsonArrays($originalPackageJson, $newPackageJson);

        file_put_contents($packageJson, json_encode($mergedPackageJson, JSON_PRETTY_PRINT));


    }

    protected function mergeJsonArrays($priority_json, $original_json_content)
    {
        foreach ($original_json_content as $org_content_key => $org_content_value) {
            if (!array_key_exists($org_content_key, $priority_json)) {
                $priority_json[$org_content_key] = $org_content_value;
            } elseif (!is_string($org_content_value)) {
                $priority_json[$org_content_key] =  $this->mergeJsonArrays($priority_json[$org_content_key], $org_content_value);
            }
        }
        return $priority_json;
    }

}
