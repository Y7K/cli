<?php

namespace Y7K\Cli;

use Y7K\Cli\Util;
use Symfony\Component\Yaml\Yaml;

class ComponentsUtil
{

    public static function printFiles($files, $io, $group = true) {

        if($group) {
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

        } else {
            $io->listing($files);
        }

    }



    public static function printDependencies($componentConfig, $io)
    {

        if(count($componentConfig['npmDependencies']) > 0) {
            $io->section('NPM Dependencies:');

            foreach ($componentConfig['npmDependencies'] as $name => $version) {
                $io->listing([$name . ': ' . $version]);
            }
        }

        if(count($componentConfig['npmDevDependencies']) > 0) {
            $io->section('NPM Dev Dependencies:');
            foreach ($componentConfig['npmDevDependencies'] as $name => $version) {
                $io->listing([$name . ': ' . $version]);
            }
        }

        if(count($componentConfig['composerDependencies']) > 0) {
            $io->section('Composer Packages:');
            foreach ($componentConfig['composerDependencies'] as $name => $version) {
                $io->listing([$name . ': ' . $version]);
            }
        }

    }


    public static function getComponentFile($fileUrl)
    {
        $repo = 'y7k/components';
        $branch = 'master';
        $remoteFileUrl = 'https://raw.githubusercontent.com/'.$repo.'/'.$branch.'/'.$fileUrl;
        return Util::download($remoteFileUrl);
    }


    public static function getComponentConfig($componentName)
    {
        $url = 'components/' . $componentName . '.yml';
        return Yaml::parse(self::getComponentFile($url));
    }


    public static function copyFile($fileUrl)
    {
        $file = self::getComponentFile($fileUrl);
        $targetFileUrl = $fileUrl;
        $targetDirectory = dirname($targetFileUrl);
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }
        file_put_contents($targetFileUrl, $file);
    }



    public static function applyFileMerges($destinationFile, $mergeFile)
    {
        if(is_file($destinationFile)) {
            $mergeContent = ComponentsUtil::getComponentFile($mergeFile);

            $start = '<<<<<<<' . PHP_EOL;
            $end = '>>>>>>>';
            $pattern = "/$start(.*?)$end/s";

            preg_match_all($pattern, $mergeContent, $matches);


            foreach ($matches[1] as $mergePair) {
                $mergePairParts = explode('=======', $mergePair);

                $find = ltrim($mergePairParts[0]);
                $replace = ltrim($mergePairParts[1]);

                Util::findAndReplaceInFile($destinationFile, $find, $replace);

            }

        }
    }

}
