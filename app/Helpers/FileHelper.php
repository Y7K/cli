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
        $folderName = preg_replace('!.zip$!', '', $zipFile);

        $zip = new ZipArchive;

        if ($zip->open($zipFile) === true) {
            $zip->extractTo($folderName);
            $zip->close();
        } else {
            throw new \RuntimeException("Zip {$zipFile} could not be extracted");
        }

        // Delete the original zip file
        unlink($zipFile);

        if ($downloadedFromGithub) {

            // get the list of directories within our tmp folder
            $dirs = glob($folderName . '/*');

            // get the source directory from the tmp folder
            if (isset($dirs[0]) && is_dir($dirs[0])) {

                // Remove the github created subfolder
                $tempFolder = $folderName . "_2";
                $process = new Process("mv {$dirs[0]} {$tempFolder} && rm -rf {$folderName} && mv {$tempFolder} {$folderName}");
                $process->run();

            } else {
                throw new \RuntimeException('The source directory could not be found');
            }

        }

        return $folderName;
    }


    public static function copyFilesToDirectory(string $sourceFolder, array $options = [])
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

        if (!is_dir($destinationPath)) {
            if (!mkdir($destinationPath) && !is_dir($destinationPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" already exists or could not be created', $destinationPath));
            }
        }

        // Copy Directories
        foreach ($subfolders as $directory) {

            if($directory !== '/') {
                $filteredExcluded = [];
                foreach ($excluded as $excludedDir) {
                    if(strpos($excludedDir, $directory . DIRECTORY_SEPARATOR) === 0) {
                        $filteredExcluded[] = substr($excludedDir, strlen($directory));
                    }
                }
            } else {
                $filteredExcluded = $excluded;
            }

            self::copyDirectory(
                $sourceFolder . DIRECTORY_SEPARATOR . $directory,
                $destinationPath,
                $filteredExcluded
            );
        }

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


    public static function deleteDirectory($dir)
    {
        if($dir !== "/") {
            $process = new Process("rm -rf {$dir}");
            $process->run();
        }
    }


    protected static function copyDirectory($sourcePath, $destinationPath, $excluded)
    {

        if (!is_dir($sourcePath)) {
            throw new \RuntimeException("The directory {$sourcePath} could not be found");
        }

        $sourcePath = rtrim($sourcePath, '/');
        $destinationPath = rtrim($destinationPath, '/');

        $excluded[] = ".git";
        $excludedFolders = implode(' --exclude=', $excluded);

        $process = new Process("rsync -rv --exclude={$excludedFolders} {$sourcePath}/. {$destinationPath}/");
        $process->run();
    }


}
