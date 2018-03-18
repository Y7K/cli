<?php
namespace App\Commands\Components;

use App\Commands\BaseCommand;
use App\Concerns\InteractsWithGitHubApi;
use App\Concerns\LoadsDataFromLocalRepository;
use Symfony\Component\Yaml\Yaml;

abstract class BaseComponentsCommand extends BaseCommand
{

    use InteractsWithGitHubApi, LoadsDataFromLocalRepository;

    public function getComponentConfig($component, $loadFromRemote)
    {
        $configFilePath = "components/{$component}/{$component}.yml";

        $configData = ($loadFromRemote)
            ? $this->readFileOnGitHub('y7k/components', 'master', $configFilePath)
            : $this->readLocalFile('y7k/components', $configFilePath);

        $config = Yaml::parse($configData);

        return (array_key_exists('404', $config)) ? false : $config;
    }

}
