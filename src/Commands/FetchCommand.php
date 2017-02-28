<?php

namespace Y7K\Cli\Commands;

use Dotenv\Dotenv;
use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends Command
{

    protected function configure()
    {
        $this->setName('fetch')
            ->setDescription('Fetch the latest assets and Database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $projectFile = $this->dir() . '/project.json';

        if (!file_exists($projectFile)) {
            throw new RuntimeException('Project.json File not found!');
        }

        $projectData = json_decode(file_get_contents($projectFile));

        if (!isset($projectData->name)) {
            throw new RuntimeException('No name specified in project.json file!');
        }


        $dotenv = new Dotenv($this->dir());
        $dotenv->overload();
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);

        $data = [
            'name' => $projectData->name,
//            'DB_NAME' => getenv('DB_NAME'),
//            'DB_USER' => getenv('DB_USER'),
//            'DB_PASSWORD' => getenv('DB_PASSWORD'),
        ];

//        $output->writeln('Retreiving commands ...');

        $process = new Process('');
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

    }

}
