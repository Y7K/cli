<?php
/**
 * Created by PhpStorm.
 * User: joris
 * Date: 18.03.18
 * Time: 17:53
 */

namespace App\Concerns;


trait LoadsDataFromLocalRepository
{

    public function getLocalRepositoryPath($githubRepository)
    {

        $availableRepositories = [
            'y7k/plate' => 'PATH_PLATE',
            'y7k/scripts' => 'PATH_SCRIPTS',
            'y7k/style' => 'PATH_STYLE',
            'y7k/components' => 'PATH_COMPONENTS',
        ];

        if(!array_key_exists($githubRepository, $availableRepositories)) {
            $this->abort("Tried to install unkown repository from local source: {$githubRepository}.");
        }

        return env($availableRepositories[$githubRepository]);
    }

}
