<?php

namespace App\Helpers;

use Symfony\Component\Process\Process;
use ZipArchive;

class FileHelper
{

    public static function downloadToZip($url, $output = null, $auth = false)
    {
        $bar = ($output) ? $output->createProgressBar(100) : null;
        $content = self::downloadContent($url, $bar, $auth);

        $tempFile = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . uniqid('y7k_', false) . '.zip';
        file_put_contents($tempFile, $content);

        return $tempFile;
    }

    public static function unzip(string $zipFile, bool $downloadedFromGithub = false)
    {
        // build the temporary folder path
        $tmpFolder = preg_replace('!.zip$!', '', $zipFile);

        $zip = new ZipArchive;

        if ($zip->open($zipFile) === true) {
            $zip->extractTo($tmpFolder);
            $zip->close();
        } else {
            throw new \RuntimeException("Zip {$zipFile} could not be extracted");
        }

        // Delete the original zip file
        unlink($zipFile);

        if ($downloadedFromGithub) {

            // get the list of directories within our tmp folder
            $dirs = glob($tmpFolder . '/*');

            // get the source directory from the tmp folder
            if (isset($dirs[0]) && is_dir($dirs[0])) {

                $tmpName = uniqid('y7k_', false);

                // Remove the github creted subfolder
                $commands = [
                    "mv {$dirs[0]} /tmp/{$tmpName}",
                    "rm -rf {$tmpFolder}",
                ];

                $process = new Process(implode(' && ', $commands));
                $process->run();

                $tmpFolder = '/tmp/' . $tmpName;

            } else {
                throw new \RuntimeException('The source directory could not be found');
            }

        }

        return $tmpFolder;
    }


    public static function extractFilesToDirectory(string $sourceFolder, array $options = [])
    {
        $options = array_merge([
            'destinationPath' => null,
            'subfolders' => [DIRECTORY_SEPARATOR],
            'excluded' => [],
        ], $options);

        $destinationPath = $options['destinationPath'];
        $subfolders = $options['subfolders'];
        $excluded = $options['excluded'];

        if($destinationPath === null) throw new \RuntimeException("No destination path set.");

        // Fix options
        if(!is_array($subfolders)) $subfolders = [$subfolders];
        if(!is_array($excluded)) $excluded = [$excluded];

        // Delete Excluded files
        foreach ($excluded as $directory) {
            self::deleteDirectory($sourceFolder . DIRECTORY_SEPARATOR . $directory);
        }

        if (!is_dir($destinationPath)) {
            if (!mkdir($destinationPath) && !is_dir($destinationPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" already exists or could not be created', $destinationPath));
            }
        }

        // Copy Directories
        foreach ($subfolders as $directory) {

            $directory = $sourceFolder . DIRECTORY_SEPARATOR . $directory;

            if (!is_dir($directory)) {
                throw new \RuntimeException("The subdirectory {$directory} could not be found");
            }

            self::copyDirectory($directory, $destinationPath);
        }

        // Remove temp folder
        self::deleteDirectory($sourceFolder);
    }


    public static function downloadContent($url, $bar = null, $auth = false)
    {
        $curl = curl_init();

        if ($auth) {
            curl_setopt($curl, CURLOPT_USERPWD, $auth);
        }

        $t_vers = curl_version();
        curl_setopt($curl, CURLOPT_USERAGENT, 'curl/' . $t_vers['version']);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($bar !== null) {

            $progress = function ($resource, $total, $downloaded) use ($bar) {
                if ($downloaded && $total) {
                    $bar->setProgress(round($downloaded / $total, 2) * 100);
                }
            };

            curl_setopt($curl, CURLOPT_BUFFERSIZE, 128);
            curl_setopt($curl, CURLOPT_NOPROGRESS, false);
            curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, $progress);
        }

        $content = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if (!empty($error)) {
            throw new \RuntimeException('Download failed: ' . $url);
        }

        return $content;
    }

    protected static function deleteDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir, SCANDIR_SORT_NONE);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (is_dir($dir . "/" . $object)) {
                        self::deleteDirectory($dir . "/" . $object);
                    } else {
                        if (file_exists($dir . "/" . $object)) unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        } else {
            if (file_exists($dir)) {
                unlink($dir);
            }
        }
    }

    protected static function copyDirectory($sourcePath, $destinationPath, $subfolder = '')
    {
        $newSource = $sourcePath . $subfolder;

        foreach (array_diff(scandir($newSource, SCANDIR_SORT_NONE), ['.', '..']) as $name) {

            $filename = $subfolder . '/' . $name;
            $destinationName = $destinationPath . $filename;
            $sourceName = $newSource . '/' . $name;

            if (is_dir($sourceName) && file_exists($destinationName)) {
                self::copyDirectory($sourcePath, $destinationPath, $subfolder . '/' . $name);
            } else if (!rename($sourceName, $destinationName)) {
                throw new \RuntimeException("{$name} could not be copied");
            }
        }
    }


}
