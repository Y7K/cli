<?php

namespace Y7K\Cli\Commands\Components;

use Dotenv\Dotenv;
use RuntimeException;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Y7K\Cli\Util;
use Y7K\Cli\ComponentsUtil;
use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends Command
{

    protected function configure()
    {
        $this->setName('components:list')
            ->setDescription('Lists all possible components to install')
            ->addArgument('searchQuery', InputArgument::OPTIONAL, 'Search term');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repo = 'y7k/components';
        $branch = 'master';
        $repoTreeUrl = 'https://api.github.com/repos/'.$repo.'/git/trees/'.$branch;
        $repoTree = $this->getGithubTree($repoTreeUrl);
        $componentsFolderUrl = $repoTree[array_search('components', array_column($repoTree, 'path'))]->url;
        $componentsTree = $this->getGithubTree($componentsFolderUrl);

        $searchQuery = strtolower($input->getArgument('searchQuery'));

        if($searchQuery) {
            $componentsTree = array_filter($componentsTree, function($component) use ($searchQuery) {
                return strpos(strtolower($component->path), strtolower($searchQuery)) !== false;
            });
        }

        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Components');
        $this->io->text('Use <fg=blue>y7k components:install [componentName]</> to install a component');
        $this->io->text('Use <fg=blue>y7k components:list [search term]</> to filter the list');
        $this->io->newLine();
        $outputTable = [];
        foreach ($componentsTree as $component) {
            $componentTree = $this->getGithubTree($component->url);
            $configFileUrl = $componentTree[array_search('.yml', array_column($componentTree, 'path'))]->url;

            $configFileNodes = array_filter($componentTree, function($node) {
                return strpos($node->path, '.yml') !== false;
            });

            $configFileUrl = array_values($configFileNodes)[0]->url;

            $fileContentRaw = Util::download($configFileUrl);
            $componentConfig = Yaml::parse(base64_decode(json_decode($fileContentRaw)->content));
            $componentName = $componentConfig['name'];
            $componentTitle = $componentConfig['title'];
            $componentDescription = $componentConfig['description'];
            array_push($outputTable, [$componentName, $componentTitle, $componentDescription]);
        }

        $this->io->table(
            ['Name', 'Title', 'Description'],
            $outputTable
        );

    }


    private function getGithubTree($url)
    {
        $treeRaw = Util::download($url);
        $treeRaw = json_decode($treeRaw);
        return $treeRaw->tree;
    }

}
