<?php

namespace Y7K\Cli\Commands\Install;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Craft extends Command {

  protected function configure() {
    $this->setName('install:craft')
         ->setDescription('Installs the Craft CMS plate')
        ->addArgument('path', InputArgument::OPTIONAL, 'Directory to install into')
//         ->addOption('dev', null, InputOption::VALUE_NONE, 'Set to download the dev version from the develop branch')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $this->install([
      'repo'    => 'getkirby/starterkit',
      'branch'  => 'master',
      'path'    => $this->dir() . '/kirby',
      'output'  => $output,
      'success' => 'The core is installed!',
    ]);


      $output->writeln('Updating the craft...' . $input->getArgument('path'));

  }

}
