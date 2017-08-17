<?php

namespace Y7K\Cli;

use Dotenv\Dotenv;
use ZipArchive;
use RuntimeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;

class Util
{

    public static function unzip($file, $to)
    {

        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('The ZipArchive class is not available');
        }

        $zip = new ZipArchive;

        if ($zip->open($file) === true) {
            $zip->extractTo($to);
            $zip->close();
            return true;
        } else {
            return false;
        }

    }

    public static function download($url, $progress = null)
    {

        $dotenv = new Dotenv(__DIR__ . '/..');
        $dotenv->load();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_USERPWD, getenv('GITHUB_USER') . ':' . getenv('GITHUB_TOKEN'));

        $t_vers = curl_version();
        curl_setopt($curl, CURLOPT_USERAGENT, 'curl/' . $t_vers['version']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if (is_callable($progress)) {
            curl_setopt($curl, CURLOPT_BUFFERSIZE, 128);
            curl_setopt($curl, CURLOPT_NOPROGRESS, false);
            curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, $progress);
        }

        $content = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if (!empty($error)) {
            throw new RuntimeException('Download failed: ' . $url);
        }

        return $content;

    }

    public static function remove($item)
    {

        // delete a folder and all its contents
        if (is_dir($item)) {

            $iterator = new RecursiveDirectoryIterator($item, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }

            rmdir($item);

            // delete a file
        } else if (is_file($item)) {
            return unlink($item);
        }

    }


    public static function findAndReplaceInFile($file, $find, $replace)
    {
        //read the entire string
        $str = file_get_contents($file);

        //replace something in the file string - this is a VERY simple example
        $str = str_replace("$find", "$replace", $str);

        //write the entire string
        file_put_contents($file, $str);
    }


    public static function copyFile($file, $target)
    {
        copy($file, $target);
    }



    public static function getCliConfig()
    {
        $cliFile = getcwd() . '/.y7k-cli.yml';

        if (!file_exists($cliFile)) {
            throw new RuntimeException('.y7k-cli.yml File not found!');
        }

        return Yaml::parse(file_get_contents($cliFile));
    }



    public static function mergeJsonArrays($priority_json, $original_json_content)
    {
        foreach ($original_json_content as $org_content_key => $org_content_value) {
            if (!array_key_exists($org_content_key, $priority_json)) {
                $priority_json[$org_content_key] = $org_content_value;
            } elseif (!is_string($org_content_value)) {
                $priority_json[$org_content_key] =  self::mergeJsonArrays($priority_json[$org_content_key], $org_content_value);
            }
        }
        return $priority_json;
    }

}
