<?php

namespace Y7K\Cli\Commands\Assets;

use Dotenv\Dotenv;
use RuntimeException;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Y7K\Cli\Command;
use Y7K\Cli\Util;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssetsPullCommand extends Command
{

    protected function configure()
    {
        $this->setName('assets:pull')
            ->setDescription('⬇  Pull the assets from a specified environment to local')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment name (defined in .y7k-cli.json)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Answers all security questions with yes automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $cliData = Util::getCliConfig();
        $environment = strtolower($input->getArgument('environment'));
        $sourceData = $cliData['environments'][$environment];
        $destinationData = $cliData['environments']['local'];
        $this->validateEnvironmentConfig($environment, $sourceData);
        $this->validateEnvironmentConfig('local', $destinationData);


        if(!$input->getOption('force')) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('This will <fg=red;options=bold>OVERWRITE</> (<options=bold>local</>) with (<options=bold>'.$environment.'</>) assets. Are you sure? [yes/no]' . PHP_EOL, false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }


        $cmd =
        'rsync -avz --delete-excluded --exclude=".*" '.
        $sourceData['sshuser']
        .'@'.
        $sourceData['host']
        .':'.
        $sourceData['storage']
        . '/ ' .
        $destinationData['storage'];

        $output->writeln('<fg=blue>Pulling Assets from ('.$environment.') to (local)!</>');
        exec($cmd, $result);
        $output->writeln($result);
        $output->writeln('<fg=white;bg=blue>Done!</>');


    }



    protected function validateEnvironmentConfig($envName, $envData)
    {
        if(!$envData) throw new RuntimeException($envName . ': No definitions for this environment found!');
        if(!$envData['host']) throw new RuntimeException($envName . ': Host ("host") is not defined');
        if(!$envData['sshuser']) throw new RuntimeException($envName . ': SSH-User ("sshuser") is not defined');
    }

}
