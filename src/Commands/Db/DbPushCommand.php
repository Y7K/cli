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

class DbPushCommand extends Command
{

    protected function configure()
    {
        $this->setName('db:push')
            ->setDescription('⬆  Push the local database to a specified environment')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment name (defined in .y7k-cli.json)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $cliData = Util::getCliConfig();
        $environment = strtolower($input->getArgument('environment'));
        $destinationData = $cliData['environments'][$environment];
        $sourceData = $cliData['environments']['local'];
        $this->validateEnvironmentConfig($environment, $sourceData);
        $this->validateEnvironmentConfig('local', $destinationData);


        if(array_key_exists('production', $destinationData) && $destinationData['production'] == 'true') {

            $helper = $this->getHelper('question');

            $question = new ConfirmationQuestion(
                'This will <fg=red;options=bold>OVERWRITE</> production! Are you really, really sure? Type <bg=yellow;options=bold> i fucking know what im doing </></> if you want to proceed:' . PHP_EOL,
                false,
                '/^(I fucking know what im doing)/i'
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<fg=red>Srsly, Bro?</>');
                return;
            }
        } else {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('This will <fg=red;options=bold>OVERWRITE</> (<options=bold>'.$environment.'</>) with (<options=bold>local</>) database. Are you sure? [yes/no]' . PHP_EOL, false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }


        $cmd =
        'ssh '.
        $sourceData['sshuser']
        .'@'.
        $sourceData['host']
        .' -p 2222 "mysqldump --opt --user='.
        $sourceData['dbuser']
        .' --password='.
        $sourceData['password']
        .' '.
        $sourceData['db']
        .'" | ssh '.
        $destinationData['sshuser']
        .'@'.
        $destinationData['host']
        .' "mysql --user='.
        $destinationData['dbuser']
        .' --password='.
        $destinationData['password']
        .' '.
        $destinationData['db']
        .'"';

        $output->writeln('<fg=blue>Pushing DB from (local) to ('.$environment.')!</>');
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
