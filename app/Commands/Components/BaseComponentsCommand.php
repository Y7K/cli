<?php
namespace App\Commands\Components;

use App\Commands\BaseCommand;
use App\Concerns\InteractsWithGitHubApi;
use App\Concerns\LoadsDataFromLocalRepository;
use Symfony\Component\Yaml\Yaml;

abstract class BaseComponentsCommand extends BaseCommand
{

    use InteractsWithGitHubApi, LoadsDataFromLocalRepository;

    public function getComponentConfig($component, $loadFromLocal)
    {
        $configFilePath = "{$component}.yml";

        $configData = $this->getComponentFile($component, $configFilePath, $loadFromLocal);

        $config = Yaml::parse($configData);

        if(array_key_exists('404', $config)) {
            $this->abort("Component {$component} not found!");
        }

        return $config;
    }

    public function getComponentFile($component, $fileUrl, $loadFromLocal) {

        $fileUrl = "components/{$component}/{$fileUrl}";

        return ($loadFromLocal)
            ? $this->readLocalFile('y7k/components', $fileUrl)
            : $this->readFileOnGitHub('y7k/components', 'master', $fileUrl);
    }

}
