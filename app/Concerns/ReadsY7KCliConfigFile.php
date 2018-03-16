<?php

namespace App\Concerns;

use Symfony\Component\Yaml\Yaml;

trait ReadsY7KCliConfigFile
{

    public function getCliConfigData()
    {
        $cliFile = getcwd() . '/.y7k-cli.yml';

        if (!file_exists($cliFile)) {
            $this->abort('.y7k-cli.yml File not found!');
        }

        return Yaml::parse(file_get_contents($cliFile));
    }

}
