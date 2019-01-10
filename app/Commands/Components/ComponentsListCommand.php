<?php

namespace App\Commands\Components;


use App\Helpers\FileHelper;
use App\Helpers\GitHubApiHelper;

class ComponentsListCommand extends BaseComponentsCommand
{

    protected $signature = 'components:list ' .
    '{--l|local : Load from local repository instead of remote source?}';
    protected $description = '🔍  Lists all components available to install';


    public function handle(): void
    {

        $loadFromLocal = $this->option('local');

        $components = ($loadFromLocal)
            ? $this->listLocalComponents()
            : $this->listRemoteComponents();

        $componentTable = [];

        foreach($components as $component) {
            $config = $this->getComponentConfig($component, $loadFromLocal);
            $componentTable[] = [$config['name'], $component, $config['description']];
        }

        $this->line("");
        $this->table(['Name', 'component', 'Description'], $componentTable);

    }

    public function listRemoteComponents()
    {
        $repoTree = $this->getTreeOfRepo('y7k/components');
        $componentsFolderUrl = $repoTree[array_search('components', array_column($repoTree, 'path'))]->url;
        $componentsTree = $this->getTreeOfUrl($componentsFolderUrl);

        $componentPaths = [];
        foreach ($componentsTree as $component) {
            $componentPaths[] = $component->path;
        }

        return $componentPaths;
    }

    public function listLocalComponents()
    {
        $path = $this->getLocalRepositoryPath('y7k/components') . '/components';

        $componentPaths = [];
        foreach (glob($path . '/*', GLOB_ONLYDIR) as $dir) {
            $explodedPath = explode('/', $dir);
            $componentPaths[] = $explodedPath[count($explodedPath) - 1];
        }

        return $componentPaths;
    }

}
