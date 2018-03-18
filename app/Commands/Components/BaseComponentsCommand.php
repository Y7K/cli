<?php
namespace App\Commands\Components;

use App\Commands\BaseCommand;
use App\Concerns\InteractsWithGitHubApi;
use App\Concerns\LoadsDataFromLocalRepository;

abstract class BaseComponentsCommand extends BaseCommand
{

    use InteractsWithGitHubApi, LoadsDataFromLocalRepository;

}
