<?php

namespace Y7K\Cli\Commands\Db;

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

class DbPullCommand extends Command
{

    protected function configure()
    {
        $this->setName('db:pull')
            ->setDescription('Pull a database from a specified environment to local')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment name (defined in .y7k-cli.json)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $cliData = Util::getCliConfig();
        $environment = strtolower($input->getArgument('environment'));
        $sourceData = $cliData['environments'][$environment];
        $destinationData = $cliData['environments']['local'];
        $this->validateEnvironmentConfig($environment, $sourceData);
        $this->validateEnvironmentConfig('local', $destinationData);


        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('This will <fg=red;options=bold>OVERWRITE</> (<options=bold>local</>) with (<options=bold>'.$environment.'</>) database. Are you sure? [yes/no]' . PHP_EOL, false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }


        $cmd =
        'ssh '.
        $sourceData['sshuser']
        .'@'.
        $sourceData['host']
        .' "mysqldump --opt --user='.
        $sourceData['dbuser']
        .' --password='.
        $sourceData['password']
        .' '.
        $sourceData['db']
        .'" | ssh '.
        $destinationData['sshuser']
        .'@'.
        $destinationData['host']
        .' -p 2222 "mysql --user='.
        $destinationData['dbuser']
        .' --password='.
        $destinationData['password']
        .' '.
        $destinationData['db']
        .'"';

        $output->writeln('<fg=blue>Pulling DB from ('.$environment.') to (local)!</>');
        exec($cmd, $result);
        $output->write($result);
        $output->writeln('<fg=white;bg=blue>Done!</>');


    }



    protected function validateEnvironmentConfig($envName, $envData)
    {
        if(!$envData) throw new RuntimeException($envName . ': No definitions for this environment found!');
        if(!$envData['host']) throw new RuntimeException($envName . ': Host ("host") is not defined');
        if(!$envData['db']) throw new RuntimeException($envName . ': DB-Name ("db") is not defined');
        if(!$envData['sshuser']) throw new RuntimeException($envName . ': SSH-User ("sshuser") is not defined');
        if(!$envData['dbuser']) throw new RuntimeException($envName . ': DB-User ("dbuser") is not defined');
        if(!$envData['password']) throw new RuntimeException($envName . ': Password ("password") is not defined');
    }

}
