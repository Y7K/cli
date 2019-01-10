<?php
namespace App\Helpers;


class GitHubApiHelper
{

    public static function getTree($url, $output = null, $auth = false)
    {
        $bar = ($output) ? $output->createProgressBar(100) : null;
        $treeRaw = FileHelper::downloadContent($url, $bar, $auth);
        $treeRaw = json_decode($treeRaw);

        if (property_exists($treeRaw, 'message')) {
            throw new \RuntimeException($treeRaw->message);
        }

        return $treeRaw->tree;
    }

}
