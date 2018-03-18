<?php

namespace App\Commands\Components;


use App\Helpers\FileHelper;
use App\Helpers\GitHubApiHelper;

class ComponentsListCommand extends BaseComponentsCommand
{

    protected $signature = 'components:list ' .
    '{--r|remote : Load Components from online repository instead of local source?}';
    protected $description = '🔍  Lists all components available to install';


    public function handle(): void
    {

        $componentPaths = ($this->option('remote'))
            ? $this->listRemoteComponents()
            : $this->listLocalComponents();

//        var_dump($componentPaths);

//        $this->table(['name'], $componentPaths);

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
