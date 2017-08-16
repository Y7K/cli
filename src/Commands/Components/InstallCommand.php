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

        $componentName = strtolower($input->getArgument('component'));
        $componentConfig = ComponentsUtil::getComponentConfig($componentName);

        if(array_key_exists('404', $componentConfig)) {
            throw new RuntimeException('Component not found!');
        }

        $this->copyFiles($componentConfig);
        $this->mergeFiles($componentConfig);
        $this->installDependencies($componentConfig);


        $this->io->title('Installed:');
        ComponentsUtil::printFiles($componentConfig['files'], $this->io);
        ComponentsUtil::printDependencies($componentConfig, $this->io);
        $this->io->text('<fg=green;options=bold>Done installing!</>');

    }


    private function copyFiles($componentConfig)
    {
        $this->io->text('<fg=blue>Copying files...</>');

        $this->io->progressStart(count($componentConfig['files']));

        foreach ($componentConfig['files'] as $fileUrl) {
            $file = ComponentsUtil::copyFile($fileUrl);
            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

    }


    private function mergeFiles($componentConfig)
    {
        $this->io->text('<fg=blue>Merging files...</>');
    }


    private function installDependencies($componentConfig)
    {
        $this->io->text('<fg=blue>Installing dependencies...</>');
    }

}
