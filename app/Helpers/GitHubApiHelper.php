<?php
namespace App\Helpers;


class GitHubApiHelper
{

    public static function getTree($url, $output = null, $auth = false)
    {
        $bar = ($output) ? $output->createProgressBar(100) : null;
        $treeRaw = FileHelper::downloadContent($url, $bar, $auth);
        return json_decode($treeRaw)->tree;
    }

}
