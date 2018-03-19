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
        $configFilePath = "{$component}.yml";

        $configData = $this->getComponentFile($component, $configFilePath, $loadFromRemote);

        $config = Yaml::parse($configData);

        if(array_key_exists('404', $config)) {
            $this->abort("Component {$component} not found!");
        }

        return $config;
    }

    public function getComponentFile($component, $fileUrl, $loadFromRemote) {

        $fileUrl = "components/{$component}/{$fileUrl}";

        return ($loadFromRemote)
            ? $this->readFileOnGitHub('y7k/components', 'master', $fileUrl)
            : $this->readLocalFile('y7k/components', $fileUrl);
    }

}