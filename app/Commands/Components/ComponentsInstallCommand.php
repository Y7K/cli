<?php

namespace App\Commands\Components;


class ComponentsInstallCommand extends BaseComponentsCommand
{

    protected $signature = 'components:install ' .
    '{component : Component name}' .
    '{--r|remote : Load Components from online repository instead of local source?}';
    protected $description = '⏳  Install a new component into the project';


    public function handle(): void
    {

        $loadFromRemote = $this->option('remote');
        $component = $this->argument('component');

        $componentConfig = $this->getComponentConfig($component, $loadFromRemote);

        $this->info("Installing Component {$componentConfig['name']}...");

        $this->copyFiles($componentConfig, $loadFromRemote);
        $this->mergeFiles($componentConfig, $loadFromRemote);
        $this->installNpmDependencies($componentConfig);
        $this->installComposerDependencies($componentConfig);

        $this->info("Component {$componentConfig['name']} installed!");
    }


    



}
