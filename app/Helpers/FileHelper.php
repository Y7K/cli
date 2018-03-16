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

        $tempFile = '/tmp/' . uniqid('y7k_', false) . '.zip';
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

        unlink($zipFile);

        if($downloadedFromGithub) {

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


    public static function extractFilesToDirectory(array $folders, string $destinationPath, array $exclude = [])
    {


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


}
