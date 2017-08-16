<?php

namespace Y7K\Cli;

use Y7K\Cli\Util;
use Symfony\Component\Yaml\Yaml;

class ComponentsUtil
{

    public static function printFiles($files, $io) {

        $jsFiles = array_filter($files, function($path) {
            return strpos($path, '/js/') !== false;
        });

        $cssFiles = array_filter($files, function($path) {
            return strpos($path, '/scss/') !== false;
        });

        $twigFiles = array_filter($files, function($path) {
            return strpos($path, '.twig') !== false;
        });

        $otherFiles = array_diff($files, $jsFiles, $cssFiles, $twigFiles);

        if(count($jsFiles) > 0) {
            $io->section('JS Files:');
            $io->listing($jsFiles);
        }

        if(count($cssFiles) > 0) {
            $io->section('Stylesheets:');
            $io->listing($cssFiles);
        }

        if(count($twigFiles) > 0) {
            $io->section('Twig Files:');
            $io->listing($twigFiles);
        }

        if(count($otherFiles) > 0) {
            $io->section('Other Files:');
            $io->listing($otherFiles);
        }

    }



    public static function printDependencies($componentConfig, $io)
    {

        if(count($componentConfig['npmDependencies']) > 0) {
            $io->section('NPM Dependencies:');
            $io->listing($componentConfig['npmDependencies']);
        }

        if(count($componentConfig['npmDevDependencies']) > 0) {
            $io->section('NPM Dev Dependencies:');
            $io->listing($componentConfig['npmDevDependencies']);
        }

        if(count($componentConfig['composerDependencies']) > 0) {
            $io->section('Composer Packages:');
            $io->listing($componentConfig['composerDependencies']);
        }

    }


    public static function getComponentConfig($componentName)
    {
        $repo = 'y7k/components';
        $branch = 'develop';
        $file = $componentName;
        $configFileUrl = 'https://raw.githubusercontent.com/'.$repo.'/'.$branch.'/components/'.$file . '.yml';
        $fileContentRaw = Util::download($configFileUrl);
        return Yaml::parse($fileContentRaw);
    }


    public static function copyFile($fileUrl)
    {
        $repo = 'y7k/components';
        $branch = 'develop';
        $remoteFileUrl = 'https://raw.githubusercontent.com/'.$repo.'/'.$branch.'/'.$fileUrl . '.yml';
        $targetFileUrl = $fileUrl;
        $targetDirectory = dirname($targetFileUrl);
        $file = Util::download($remoteFileUrl);

        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }
        file_put_contents($targetFileUrl, $file);
    }

}
