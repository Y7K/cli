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

class UninstallCommand extends Command
{

    protected function configure()
    {
        $this->setName('components:uninstall')
            ->setDescription('❌  Removes a component from the project')
            ->addArgument('component', InputArgument::REQUIRED, 'Component name')
            ->addOption('remote', 'r', InputOption::VALUE_NONE, 'Load Components from online repository instead from local?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->output = $output;

        $componentName = $input->getArgument('component');
        $isRemote = $input->getOption('remote');
        $componentConfig = ComponentsUtil::getComponentConfig($componentName, $isRemote);

        if(array_key_exists('404', $componentConfig)) {
            throw new RuntimeException('Component not found!');
        }

        $this->io->title('Removing Component...');

        $this->removeFiles($componentConfig);
        $this->removeFileMerges($componentConfig);
        $this->removeNpmDependencies($componentConfig);
        $this->removeComposerDependencies($componentConfig);

        $this->io->newLine(2);
        $this->io->text('<fg=green;options=bold>Component removed!</>');
        $this->io->newLine(2);

    }


    private function removeFiles($componentConfig)
    {

        $this->io->section('Files');
        $this->io->text('<fg=blue>Removing files...</>');

        $progressBar = $this->io->createProgressBar(count($componentConfig['files']));

        foreach ($componentConfig['files'] as $fileUrl) {
            $progressBar->advance();
            if(is_file($fileUrl)) {
                unlink($fileUrl);
            }
                $progressBar->clear();
                $this->io->text('* ' . $fileUrl);
                $progressBar->display();
        }

        $progressBar->finish();
        $this->io->newLine(2);


    }


    private function removeFileMerges($componentConfig)
    {

        if(empty($componentConfig['filemerges'])) return;

        $this->io->section('File Merges');
        $this->io->note('Files that were modified by file merging do not get reset. Please remove traces yourself in the following files:');
        $fileDestinations = array_map(create_function('$a', 'return $a[\'dest\'];'), $componentConfig['filemerges']);
        $this->io->listing($fileDestinations);
    }


    private function removeNpmDependencies($componentConfig)
    {
        if(empty($componentConfig['npmDependencies']) && empty($componentConfig['npmDevDependencies'])) return;

        $this->io->section('NPM Dependencies');
        $this->io->note('package.json will not be modified, Please remove dependencies yourself, when necessary:');
        foreach ($componentConfig['npmDependencies'] as $name => $version) {
            $this->io->listing([$name . ': ' . $version]);
        }
    }


    private function removeComposerDependencies($componentConfig)
    {
        if(empty($componentConfig['composerDependencies'])) return;

        $this->io->section('Composer Dependencies');
        $this->io->note('composer.json will not be modified, Please remove dependencies yourself, when necessary:');
        foreach ($componentConfig['npmDependencies'] as $name => $version) {
            $this->io->listing([$name . ': ' . $version]);
        }
    }
}
