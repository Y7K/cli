<?php

namespace Y7K\Cli;

use RuntimeException;
use Y7K\Cli\Util;

class Command extends \Symfony\Component\Console\Command\Command
{

    protected function dir()
    {
        return getcwd();
    }

    protected function tmp($filename)
    {
        return '/tmp/' . $filename;
    }

    protected function checkPath($path)
    {

        if (count(glob($path . '/*')) !== 0) {
            throw new RuntimeException('The folder is not empty: ' . realpath($path));
        }

        if (is_dir($path)) {
            throw new RuntimeException('The folder exists and cannot be overwritten: ' . realpath($path));
        }

    }

    protected function downloadFromGitHub($params)
    {

        $options = array_merge([
            'repo' => 'y7k/plate',
            'branch' => 'master',
            'zip' => null,
            'output' => null
        ], $params);

        extract($options);

        if (!$zip) {
            throw new RuntimeException('Please provide a zip file');
        }

        // build the download url
        $url = 'https://api.github.com/repos/' . $repo . '/zipball/' . $branch;

        // generate some usable output
        if ($output) {
            $output->writeln('<info>Downloading from: ' . $url . '</info>');
        }

        // send the remote request
        $download = Util::download($url, function ($resource, $total, $downloaded) use ($output) {

            if (!$output) return null;

            if ($downloaded && $total) {
                $output->write('Downloaded: ' . round($downloaded / $total, 2) * 100 . "%\r");
            }

        });

        // write the result to the disk
        file_put_contents($zip, $download);

    }

    protected function unzip($zip, $path, $subfolders = '/')
    {

        // build the temporary folder path
        $tmp = substr($this->tmp(preg_replace('!.zip$!', '', $zip)),5);

        // extract the zip file
        Util::unzip($zip, $tmp);

        // get the list of directories within our tmp folder
        $dirs = glob($tmp . '/*');

        // get the source directory from the tmp folder
        if(isset($dirs[0]) && is_dir($dirs[0])) {
            $source = $dirs[0];
        } else {
            throw new RuntimeException('The source directory could not be found');
        }

        if (!is_array($subfolders)) $subfolders = [$subfolders];

        // Loop through all subfolders and extract them
        foreach ($subfolders as $subfolder) {

            $subSource = $source . '/' . ltrim($subfolder, '/');

            // get the source directory from the tmp folder
            if (!is_dir($subSource)) {
                throw new RuntimeException('The subdirectory could not be found');
            }

            // create the folder if it does not exist yet
            if (!is_dir($path)) mkdir($path);

            // extract the content of the directory to the final path
            $this->copyDirectory($path, $subSource);

        }
        // remove the zip file
        Util::remove($zip);

        // remove the temporary folder
        Util::remove($tmp);

    }

    protected function copyDirectory($path, $source, $subfolder = '')
    {
        $newSource = $source . $subfolder;

        foreach ((array)array_diff(scandir($newSource), ['.', '..']) as $name) {

            $destinationName = $path . $subfolder . '/' . $name;
            $sourceName = $newSource . '/' . $name;
            
            if(is_dir($sourceName) && file_exists($destinationName)) {
                $this->copyDirectory($path, $source,  $subfolder . '/' . $name);
            } else if (!rename($sourceName, $destinationName)) {
                throw new RuntimeException($name . ' could not be copied');
            }
        }
    }

    protected function install($params = [])
    {

        $options = array_merge([
            'repo' => 'y7k/plate',
            'branch' => 'master',
            'path' => null,
            'output' => null,
            'success' => 'Done!',
            'subfolders' => 'base'
        ], $params);

        // check for a valid path
        $this->checkPath($options['path']);

        // create the file name for the temporary zip file
        $zip = $this->tmp('y7k-' . str_replace('/', '-', $options['repo']) . '-' . uniqid() . '.zip');

        // download the file
        $this->downloadFromGitHub([
            'repo' => $options['repo'],
            'branch' => $options['branch'],
            'zip' => $zip,
            'output' => $options['output'],
        ]);

        // unzip the file
        $this->unzip($zip, $options['path'], $options['subfolders']);

        // yay, everything is setup
        if ($options['output'] && $options['success']) {
            $options['output']->writeln('');
            $options['output']->writeln('<comment>' . $options['success'] . '</comment>');
            $options['output']->writeln('');
        }

    }

}
