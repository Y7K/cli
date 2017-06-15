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

class Stylesheets extends Command
{

    protected function configure()
    {
        $this->setName('install:stylesheets')
            ->setDescription('Install SCSS Boilerplate')
            ->addArgument('path', InputArgument::REQUIRED, 'Where does the Project live in?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Load Question Helper
        $helper = $this->getHelper('question');


        // Get Paths
        $path = $input->getArgument('path');
        $filepath = $this->dir() . '/' . $path;

        $output->writeln('');
        $output->writeln('Installing the <info>' . ucfirst($type) . '</info> SCSS Boilerplate...');
        $output->writeln('');

        // Install the repo
        $this->install([
            'repo' => 'y7k/style',
            'branch' => 'develop',
            'path' => $filepath . '/resources/assets',
            'output' => $output,
            'subfolders' => ['source' ],
            'success' => 'The SCSS boilerplate has been loaded!',
            'checkPath' => false
        ]);

        // Merge the package.json files
        $packageJson = $filepath. '/package.json';
        $newPackageJsonFilepath = $filepath . '/resources/assets/package.json';

        $originalPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : NULL;
        $newPackageJson = is_file($newPackageJsonFilepath) ? json_decode(file_get_contents($newPackageJsonFilepath), true) : NULL;
        $mergedPackageJson = $this->mergeJsonArrays($originalPackageJson, $newPackageJson);

        // Delete the js package.json
        unlink($newPackageJsonFilepath);

        // Write to project package.json
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
