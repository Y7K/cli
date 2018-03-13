<?php

namespace Y7K\Cli\Commands\Install\Platform;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Y7K\Cli\Util;

class Laravel extends Command
{

    protected function configure()
    {
        $this->setName('install:laravel')
            ->setDescription('Install the Laravel Framework')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where u wanna put it, bro?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $path = $input->getArgument('path');
        $filepath = $this->dir() . ($path ? '/' . $path : '');

        $this->install([
            'repo' => 'laravel/laravel',
            'branch' => 'master',
            'path' => $filepath,
            'output' => $output,
            'exclude' => ['resources/assets', 'public/css', 'public/js', 'resources/views'],
            'success' => 'The Laravel Framework (Vendor) has been loaded from remote!',
            'checkPath' => false
        ]);


        if($input->getOption('remote')) {
            $this->installFromRemote([
                'repo' => 'y7k/plate',
                'branch' => 'master',
                'path' => $filepath,
                'output' => $output,
                'subfolders' => ['base', 'platforms/laravel'],
                'exclude' => ['base/.gitignore'],
                'success' => 'The Laravel Boilerplate has been loaded from remote!',
                'checkPath' => false
            ]);
        } else {
            $this->installFromLocal([
                'sourcePath' => getenv('PATH_PLATE'),
                'subfolders' => ['base', 'platforms/laravel'],
                'destPath' => $filepath,
                'output' => $output,
                'success' => 'The Laravel Boilerplate has been loaded from local!',
            ]);
        }

        $packageJson = $filepath. '/composer.json';
        $newPackageJsonFilepath = $filepath . '/composer.merge.json';

        $originalPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : [];
        $newPackageJson = is_file($newPackageJsonFilepath) ? json_decode(file_get_contents($newPackageJsonFilepath), true) : [];
        $mergedPackageJson = Util::mergeJsonArrays($originalPackageJson, $newPackageJson);

        var_dump($newPackageJson);
        var_dump($mergedPackageJson);

        // Delete the js package.json
        unlink($newPackageJsonFilepath);

        file_put_contents($packageJson, json_encode($mergedPackageJson, JSON_PRETTY_PRINT));


//        Util::findAndReplaceInFile($filepath . '/.env.example', '{name}', $path);
//        Util::findAndReplaceInFile($filepath . '/.env.example', '{code}', $path);
        Util::findAndReplaceInFile($filepath . '/composer.json', 'laravel/laravel', $path);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install',
            'run-script post-install-cmd',
            'run-script post-create-project-cmd'
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}
