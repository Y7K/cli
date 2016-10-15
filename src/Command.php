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

    protected function download($params)
    {

        $options = array_merge([
            'repo' => 'getkirby/starterkit',
            'branch' => 'master',
            'zip' => null,
            'output' => null
        ], $params);

        extract($options);

        if (!$zip) {
            throw new RuntimeException('Please provide a zip file');
        }

        // build the download url
        $url = 'https://github.com/' . $repo . '/archive/' . $branch . '.zip';

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

    protected function unzip($zip, $path, $subfolders = null)
    {

        // build the temporary folder path
        $tmp = $this->tmp(preg_replace('!.zip$!', '', $zip));

        // extract the zip file
        Util::unzip($zip, $tmp);

        // get the list of directories within our tmp folder
        $dirs = glob($tmp . '/*');

        if (!is_array($subfolders)) $subfolders = [$subfolders];

        // Loop through all subfolders and extract them
        foreach ($subfolders as $subfolder) {

            $source = $dirs[0] . '/' . $subfolder;

            // get the source directory from the tmp folder
            if (!isset($source) || !is_dir($source)) {
                throw new RuntimeException('The source directory could not be found');
            }

            // create the folder if it does not exist yet
            if (!is_dir($path)) mkdir($path);

            // extract the content of the directory to the final path
            foreach ((array)array_diff(scandir($source), ['.', '..']) as $name) {
                if (!rename($source . '/' . $name, $path . '/' . $name)) {
                    throw new RuntimeException($name . ' could not be copied');
                }
            }

        }
        // remove the zip file
        Util::remove($zip);

        // remove the temporary folder
        Util::remove($tmp);

    }

    protected function install($params = [])
    {

        $options = array_merge([
            'repo' => 'getkirby/starterkit',
            'branch' => 'master',
            'path' => null,
            'output' => null,
            'success' => 'Done!'
        ], $params);

        // check for a valid path
        $this->checkPath($options['path']);

        // create the file name for the temporary zip file
        $zip = $this->tmp('kirby-' . str_replace('/', '-', $options['repo']) . '-' . uniqid() . '.zip');

        // download the file
        $this->download([
            'repo' => $options['repo'],
            'branch' => $options['branch'],
            'zip' => $zip,
            'output' => $options['output'],
        ]);

        // unzip the file
        $this->unzip($zip, $options['path']);

        // yay, everything is setup
        if ($options['output'] && $options['success']) {
            $options['output']->writeln('');
            $options['output']->writeln('<comment>' . $options['success'] . '</comment>');
            $options['output']->writeln('');
        }

    }

}
