<?php

namespace App\Concerns;

use Symfony\Component\Yaml\Yaml;

trait ReadsY7KCliConfigFile
{

    protected $cliConfigCache;

    public function getCliConfigData()
    {
        if(!empty($this->cliConfigCache)) return $this->cliConfigCache;

        $cliFile = getcwd() . '/.y7k-cli.yml';

        if (!file_exists($cliFile)) {
            $this->abort('.y7k-cli.yml File not found!');
        }

        $this->cliConfigCache = Yaml::parse(file_get_contents($cliFile));

        return $this->cliConfigCache;
    }


    public function getCliEnvironmentData($environment)
    {
        return $this->getCliConfigData()['environments'][$environment];
    }

    public function getAvailableEnviromnents()
    {
        return array_keys($this->getCliConfigData()['environments']);
    }

    public function getValidatedEnvironmentData($environment, $requiredKeys = [])
    {
        if(!in_array($environment, $this->getAvailableEnviromnents())) {
            $this->abort("Environment \"{$environment}\" not found! Available: " . implode(", ", $this->getAvailableEnviromnents()));
        }

        foreach ($requiredKeys as $requiredKey) {
            if(!isset($this->getCliEnvironmentData($environment)[$requiredKey]) || empty($this->getCliEnvironmentData($environment)[$requiredKey])) {
                $this->abort("No value set for \"{$requiredKey}\" in \"{$environment}\" enviroment in cli config file.");
            }
        }

        return $this->getCliEnvironmentData($environment);
    }


    public function buildSshCommand($environment)
    {
        $environmentData = $this->getCliEnvironmentData($environment);
        $port = (isset($environmentData['port']) && $environmentData['port']) ? " -p " . $environmentData['port'] : '';
        return "ssh {$environmentData['sshuser']}@{$environmentData['host']}{$port}";
    }


    public function buildRemoteStoragePath($environment)
    {
        $environmentData = $this->getCliEnvironmentData($environment);
        $remotePath = rtrim($environmentData['path'], '/');
        $remoteStorage = trim($environmentData['storage'], '/');
        $remoteStoragePath = rtrim($remotePath . '/' . $remoteStorage, '/');
        return "{$environmentData['sshuser']}@{$environmentData['host']}:{$remoteStoragePath}";
    }


    public function isProduction($environment)
    {
        $environmentData = $this->getCliEnvironmentData($environment);
        return isset($environmentData['production']) && $environmentData['production'];
    }

}
