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

class AssetsPushCommand extends Command
{

    protected function configure()
    {
        $this->setName('assets:push')
            ->setDescription('Push the assets from local to a specified environment')
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
                'This will <fg=red;options=bold>OVERWRITE</> production assets! Are you really, really sure? Type <bg=yellow;options=bold> i fucking know what im doing </></> if you want to proceed:' . PHP_EOL,
                false,
                '/^(I fucking know what im doing)/i'
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<fg=red>Srsly, Bro?</>');
                return;
            }
        } else {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('This will <fg=red;options=bold>OVERWRITE</> (<options=bold>'.$environment.'</>) with (<options=bold>local</>) assets. Are you sure? [yes/no]' . PHP_EOL, false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }


        $cmd =
        'rsync -avz --delete-excluded '.
        $sourceData['storage']
        . '/ ' .
        $destinationData['sshuser']
        .'@'.
        $destinationData['host']
        .':'.
        $destinationData['storage'];

        $output->writeln('<fg=blue>Pushing Assets from (local) to ('.$environment.')!</>');
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
