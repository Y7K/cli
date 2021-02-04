<?php
/**
 * Created by PhpStorm.
 * User: joris
 * Date: 18.03.18
 * Time: 17:43
 */

namespace App\Concerns;


use App\Helpers\FileHelper;
use App\Helpers\GitHubApiHelper;

trait InteractsWithGitHubApi
{

    public function getGitHubAuth()
    {
        return env('GITHUB_USER') . ":" . env('GITHUB_TOKEN');
    }

    public function generateZipballUrl($repo, $branch = 'master')
    {
        return "https://api.github.com/repos/{$repo}/zipball/{$branch}";
    }

    public function getTreeOfRepo($repo, $branch = 'master')
    {
        $url = "https://api.github.com/repos/{$repo}/git/trees/{$branch}";
        return GitHubApiHelper::getTree($url, $this->output, $this->getGitHubAuth());
    }

    public function getTreeOfUrl($url)
    {
        return GitHubApiHelper::getTree($url, $this->output, $this->getGitHubAuth());
    }

    public function readFileOnGitHub($repo, $branch, $file)
    {
        return FileHelper::downloadContent("https://raw.githubusercontent.com/{$repo}/{$branch}/{$file}", null, $this->getGitHubAuth());
    }

}
