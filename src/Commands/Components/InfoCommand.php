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

class InfoCommand extends Command
{

    protected function configure()
    {
        $this->setName('components:info')
            ->setDescription('Shows details about a specific component')
            ->addArgument('component', InputArgument::REQUIRED, 'Component name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $componentName = $input->getArgument('component');
        $componentConfig = ComponentsUtil::getComponentConfig($componentName);

        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($componentConfig['title']);
        $this->io->text('<fg=green>'.$componentConfig['description'].'</>');
        $this->io->newLine();

        ComponentsUtil::printFiles($componentConfig['files'], $this->io);
        ComponentsUtil::printDependencies($componentConfig, $this->io);

        // $this->io->section('Example & Documentation:');
        // $exampleUrl = 'https://todo';
        // $output->writeln($exampleUrl);
    }


}
