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
    protected $loadFromRemote;

    public function handle(): void
    {

        $this->loadFromRemote = $this->option('remote');
        $this->component = $this->argument('component');
        $this->componentConfig = $this->getComponentConfig($this->component, $this->loadFromRemote);

        $this->info("Installing Component {$this->componentConfig['name']}...");

        $this
            ->copyFiles()
            ->mergeFiles()
            ->installNpmDependencies()
            ->installComposerDependencies();

        $this->info("Component {$this->componentConfig['name']} installed!");
    }


    private function copyFiles()
    {
        $this->info('<fg=blue>Copying files...</>');

        $this->installY7KRepo('components', [
            'destinationPath' => $this->getWorkingDirectory(),
            'subfolders' => ['components/' . $this->component . '/src']
        ], $this->loadFromRemote);

        return $this;
    }


    private function mergeFiles()
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
                $contensOfFileToMerge = $this->getComponentFile($this->component, $fileMerge['src'], $this->loadFromRemote);
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


    private function installNpmDependencies()
    {
        if($this->mergeJsonConfigFile('package.json')) {
            $this->info("Do not forget to run <fg=blue>npm install</>.");
        };

        return $this;
    }


    private function installComposerDependencies()
    {
        if($this->mergeJsonConfigFile('composer.json')) {
            $this->info("Do not forget to run <fg=blue>composer install</>.");
        };

        return $this;
    }

    private function mergeJsonConfigFile($fileName) {

        // Read the contents of a file as json
        $json = json_decode($this->getComponentFile($this->component, $fileName, $this->loadFromRemote));

        if($json) {
            $this->info("Merging {$fileName} file.");
            FileMergeHelper::mergeJsonIntoFile($this->getWorkingDirectory() . '/' . $fileName, $json);
        }

        return ($json !== null);
    }

}
