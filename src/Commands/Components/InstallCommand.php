<?php

namespace Y7K\Cli\Commands\Components;

use Dotenv\Dotenv;
use RuntimeException;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Y7K\Cli\Util;
use Y7K\Cli\ComponentsUtil;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class InstallCommand extends Command
{

    protected function configure()
    {
        $this->setName('components:install')
            ->setDescription('Install a new component into the project')
            ->addArgument('component', InputArgument::REQUIRED, 'Component name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->output = $output;

        $componentName = $input->getArgument('component');
        $componentConfig = ComponentsUtil::getComponentConfig($componentName);

        if(array_key_exists('404', $componentConfig)) {
            throw new RuntimeException('Component not found!');
        }

        $this->io->title('Installing Component...');

        $this->copyFiles($componentConfig);
        $this->mergeFiles($componentConfig);
        $this->installNpmDependencies($componentConfig);
        $this->installComposerDependencies($componentConfig);

        ComponentsUtil::printDependencies($componentConfig, $this->io);

        $this->io->newLine(2);
        $this->io->text('<fg=green;options=bold>Component installed!</>');
        $this->io->newLine(2);

    }


    private function copyFiles($componentConfig)
    {
        $this->io->text('<fg=blue>Copying files...</>');

        $this->io->progressStart(count($componentConfig['files']));

        foreach ($componentConfig['files'] as $fileUrl) {
            $file = ComponentsUtil::copyFile($fileUrl, $componentConfig['name']);
            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        $this->io->section('Copied Files:');
        ComponentsUtil::printFiles($componentConfig['files'], $this->io, false);

    }


    private function mergeFiles($componentConfig)
    {

        $fileDestinations = array_map(create_function('$a', 'return $a[\'dest\'];'), $componentConfig['filemerges']);

        $this->io->section('File Merging:');
        $this->io->listing($fileDestinations);
        $this->io->note('Caution! Please check file afterwards!');

        if($this->io->confirm('Do you want to apply file merges to this files?')) {
            $this->io->text('<fg=blue>Merging files...</>');

            $this->io->progressStart(count($componentConfig['filemerges']));
            foreach ($componentConfig['filemerges'] as $fileMerge) {
                ComponentsUtil::applyFileMerges($fileMerge['dest'], $fileMerge['src'], $componentConfig['name']);
                $this->io->progressAdvance();
            }
            $this->io->progressFinish();

            $this->io->section('Merged Files:');
            ComponentsUtil::printFiles($fileDestinations, $this->io, false);
        }



    }



    private function installNpmDependencies($componentConfig)
    {

        if(empty($componentConfig['npmDependencies']) && empty($componentConfig['npmDevDependencies'])) return;

        $this->io->text('<fg=blue>Adding NPM dependencies...</>');

        $packageJson = 'package.json';
        $originalPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : [];

        $newDependencies = [
            'dependencies' => $componentConfig['npmDependencies'] ? $componentConfig['npmDependencies'] : [],
            'devDependencies' => $componentConfig['npmDevDependencies'] ? $componentConfig['npmDevDependencies'] : [],
        ];

        $mergedPackageJson = Util::mergeJsonArrays($originalPackageJson, $newDependencies);
        file_put_contents($packageJson, json_encode($mergedPackageJson, JSON_PRETTY_PRINT));


        $this->io->text('<fg=blue>Installing dependencies...</>');

        if (file_exists('package.json')) {
            Util::runCommand('npm install', $this->io);
        }

        $this->io->newLine();
        $this->io->text('<fg=blue>Done adding NPM Packages!</>');
        $this->io->newLine(2);

    }



    private function installComposerDependencies($componentConfig)
    {

        if(empty($componentConfig['composerDependencies'])) return;

        $this->io->text('<fg=blue>Adding Composer dependencies...</>');

        $composerJson = 'composer.json';
        $originalComposerJson = is_file($composerJson) ? json_decode(file_get_contents($composerJson), true) : [];

        $newComposerRequires = [
            'require' => $componentConfig['composerDependencies'] ? $componentConfig['composerDependencies'] : [],
        ];

        $mergedComposerJson = Util::mergeJsonArrays($originalComposerJson, $newComposerRequires);
        file_put_contents($composerJson, json_encode($mergedComposerJson, JSON_PRETTY_PRINT));


        $this->io->text('<fg=blue>Installing dependencies...</>');

        if (file_exists('composer.json')) {
            $cmd = 'composer install';
            if (file_exists('composer.lock')) {
                $cmd = 'composer update';
            }
            Util::runCommand($cmd, $this->io);
        }

        $this->io->newLine();
        $this->io->text('<fg=blue>Done adding Composer Packages!</>');
        $this->io->newLine(2);

    }

}
