<?php

namespace App\Commands\Components;


use App\Concerns\InstallsRepository;
use App\Helpers\FileMergeHelper;

class ComponentsInstallCommand extends BaseComponentsCommand
{

    use InstallsRepository;

    protected $signature = 'components:install ' .
    '{component : Component name}' .
    '{--r|remote : Load Components from online repository instead of local source?}';
    protected $description = '⏳  Install a new component into the project';

    protected $component;
    protected $componentConfig;

    public function handle(): void
    {

        $loadFromRemote = $this->option('remote');
        $this->component = $this->argument('component');
        $this->componentConfig = $this->getComponentConfig($this->component, $loadFromRemote);

        $this->info("Installing Component {$this->componentConfig['name']}...");

        $this
            ->copyFiles($loadFromRemote)
            ->mergeFiles($loadFromRemote);
//        $this->installNpmDependencies($this->componentConfig);
//        $this->installComposerDependencies($this->componentConfig);

        $this->info("Component {$this->componentConfig['name']} installed!");
    }


    private function copyFiles($loadFromRemote)
    {
        $this->info('<fg=blue>Copying files...</>');

        $this->installY7KRepo('components', [
            'destinationPath' => $this->getWorkingDirectory(),
            'subfolders' => ['components/' . $this->component . '/source']
        ], $loadFromRemote);

        return $this;
    }


    private function mergeFiles($loadFromRemote)
    {
        $this->line("");
        $this->warn("The following files will be merged:");
        foreach (array_column($this->componentConfig['filemerges'], 'dest') as $row) {
            $this->line("– " . $row);
        }

        if($this->confirm("Do you want to proceed?")) {

            $this->info('<fg=blue>Merging files...</>');
            $bar = $this->output->createProgressBar(count($this->componentConfig['filemerges']));
            foreach ($this->componentConfig['filemerges'] as $fileMerge) {
                $contensOfFileToMerge = $this->getComponentFile($this->component, $fileMerge['src'], $loadFromRemote);
                FileMergeHelper::applyFileMerges(
                    $this->getWorkingDirectory() . '/' . $fileMerge['dest'],
                    $contensOfFileToMerge
                );
                $bar->advance();
            }

            $this->info("Files merged!");
            $this->warn("Please check the merged files!");

        } else {
            $this->info("No files were merged!");
        }

        return $this;
    }



    private function installNpmDependencies($componentConfig)
    {
//
//        if(empty($componentConfig['npmDependencies']) && empty($componentConfig['npmDevDependencies'])) return;
//
//        $this->io->text('<fg=blue>Adding NPM dependencies...</>');
//
//        $packageJson = 'package.json';
//        $originalPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : [];
//
//        $newDependencies = [
//            'dependencies' => $componentConfig['npmDependencies'] ? $componentConfig['npmDependencies'] : [],
//            'devDependencies' => $componentConfig['npmDevDependencies'] ? $componentConfig['npmDevDependencies'] : [],
//        ];
//
//        $mergedPackageJson = Util::mergeJsonArrays($originalPackageJson, $newDependencies);
//        file_put_contents($packageJson, json_encode($mergedPackageJson, JSON_PRETTY_PRINT));
//
//
//        $this->io->text('<fg=blue>Installing dependencies...</>');
//
//        if (file_exists('package.json')) {
//            Util::runCommand('npm install', $this->io);
//        }
//
//        $this->io->newLine();
//        $this->io->text('<fg=blue>Done adding NPM Packages!</>');
//        $this->io->newLine(2);

    }



    private function installComposerDependencies($componentConfig)
    {
//
//        if(empty($componentConfig['composerDependencies'])) return;
//
//        $this->io->text('<fg=blue>Adding Composer dependencies...</>');
//
//        $composerJson = 'composer.json';
//        $originalComposerJson = is_file($composerJson) ? json_decode(file_get_contents($composerJson), true) : [];
//
//        $newComposerRequires = [
//            'require' => $componentConfig['composerDependencies'] ? $componentConfig['composerDependencies'] : [],
//        ];
//
//        $mergedComposerJson = Util::mergeJsonArrays($originalComposerJson, $newComposerRequires);
//        file_put_contents($composerJson, json_encode($mergedComposerJson, JSON_PRETTY_PRINT));
//
//
//        $this->io->text('<fg=blue>Installing dependencies...</>');
//
//        if (file_exists('composer.json')) {
//            $cmd = 'composer install';
//            if (file_exists('composer.lock')) {
//                $cmd = 'composer update';
//            }
//            Util::runCommand($cmd, $this->io);
//        }
//
//        $this->io->newLine();
//        $this->io->text('<fg=blue>Done adding Composer Packages!</>');
//        $this->io->newLine(2);

    }




}
