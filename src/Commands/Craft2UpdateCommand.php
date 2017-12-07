<?php

namespace Y7K\Cli\Commands;

use RuntimeException;

use Symfony\Component\Process\Process;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Craft2UpdateCommand extends Command
{

    protected function configure()
    {
        $this->setName('craft2:update')
            ->setDescription('🔃  Update Craft 2.* to the latest version')
            ->addOption('commit', 'c', InputOption::VALUE_NONE, 'Commit update to git directly?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $craftAppDir = $this->dir() . '/craft/app';

//        if (!is_dir($craftAppDir)) {
//            throw new RuntimeException('No craft/app folder found!');
//        }

        $output->writeln('Deleting /craft/app folder...');

        $process = new Process('rm -rf ' . $craftAppDir);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $this->installFromRemote([
            'url' => 'http://craftcms.com/latest.zip?accept_license=yes',
            'path' => $craftAppDir,
            'output' => $output,
            'subfolders' => ['craft/app'],
            'success' => 'The craft app folder was updated!',
        ]);

        require_once ($craftAppDir . '/info.php');

        if($input->getOption('commit')) {
            $process = new Process('git add craft/app && git commit -m "Craft Updated to Version '. CRAFT_VERSION .'"');
            $process->run(function ($type, $line) use ($output) {
                $output->write($line);
            });
        }


        $output->writeln('<comment>' .'Craft updated to Version ' . CRAFT_VERSION.'</comment>');

    }

}
