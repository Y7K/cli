<?php

namespace Y7K\Cli;

use RuntimeException;
use Symfony\Component\Process\Process;
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

    protected function downloadFromWeb($params)
    {

        $options = array_merge([
            'url' => null,
            'zip' => null,
            'output' => null
        ], $params);

        extract($options);

        if (!$zip) {
            throw new RuntimeException('Please provide a zip file');
        }

        if (!$url) {
            throw new RuntimeException('Please provide a url');
        }

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

    protected function unzip($zip, $path, $subfolders = '/', $fromGitHub = true, $exclude = [])
    {

        // build the temporary folder path
        $tmp = substr($this->tmp(preg_replace('!.zip$!', '', $zip)), 5);

        // extract the zip file
        Util::unzip($zip, $tmp);

        $source = $tmp;

        if ($fromGitHub) {
            // get the list of directories within our tmp folder
            $dirs = glob($tmp . '/*');

            // get the source directory from the tmp folder
            if (isset($dirs[0]) && is_dir($dirs[0])) {
                $source = $dirs[0];
            } else {
                throw new RuntimeException('The source directory could not be found');
            }
        }

        foreach ($exclude as $excluded) {
            $this->deleteDirectory($source . '/' . $excluded);
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

    protected function deleteDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        $this->deleteDirectory($dir . "/" . $object);
                    else
                        if(file_exists($dir . "/" . $object)) unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        } else {
            if(file_exists($dir)) unlink($dir);
        }
    }

    protected function copyDirectory($path, $source, $subfolder = '')
    {
        $newSource = $source . $subfolder;

        foreach ((array)array_diff(scandir($newSource), ['.', '..']) as $name) {

            $filename = $subfolder . '/' . $name;
            $destinationName = $path . $filename;
            $sourceName = $newSource . '/' . $name;

            if (is_dir($sourceName) && file_exists($destinationName)) {
                $this->copyDirectory($path, $source, $subfolder . '/' . $name);
            } else if (!rename($sourceName, $destinationName)) {
                throw new RuntimeException($name . ' could not be copied');
            }
        }
    }

    protected function installFromRemote($params = [])
    {

        $options = array_merge([
            'repo' => 'y7k/plate',
            'branch' => 'master',
            'path' => null,
            'output' => null,
            'success' => 'Done!',
            'subfolders' => '/',
            'checkPath' => true,
            'exclude' => [],
            'url' => null,
        ], $params);

        // check for a valid path
        if ($options['checkPath']) $this->checkPath($options['path']);

        // create the file name for the temporary zip file
        $zip = $this->tmp('y7k-' . str_replace('/', '-', $options['repo']) . '-' . uniqid() . '.zip');

        // download the file
        if ($options['url']) {
            $this->downloadFromWeb([
                'url' => $options['url'],
                'zip' => $zip,
                'output' => $options['output'],
            ]);
        } else {
            $this->downloadFromGitHub([
                'repo' => $options['repo'],
                'branch' => $options['branch'],
                'zip' => $zip,
                'output' => $options['output'],
            ]);
        }

        // unzip the file
        $this->unzip($zip, $options['path'], $options['subfolders'], !$options['url'], $options['exclude']);

        // yay, everything is setup
        if ($options['output'] && $options['success']) {
            $options['output']->writeln('');
            $options['output']->writeln('<comment>' . $options['success'] . '</comment>');
            $options['output']->writeln('');
        }

    }


    protected function installFromLocal($params = [])
    {

        $options = array_merge([
            'sourcePath' => null,
            'subfolders' => [],
            'destPath' => null,
            'output' => null,
            'success' => 'Done!',
        ], $params);

        $success = 0;
        foreach ($options['subfolders'] as $subfolder) {
            $src = $options['sourcePath'] . '/' . $subfolder;
            $dest = $options['destPath'];
            exec("rsync -rv --exclude=.git $src/. $dest/", $output, $return);

            $success += $return;
        }


        // yay, everything is setup
        if ($options['output'] && $options['success']) {

            if($success == 0) {
                $options['output']->writeln('');
                $options['output']->writeln('<comment>' . $options['success'] . '</comment>');
                $options['output']->writeln('');

            } else {
                $options['output']->writeln('');
                $options['output']->writeln('<error>' . 'Error installing from local source...' . '</error>');
                $options['output']->writeln('');
            }
        }

    }



    protected function runComposerCommands($input, $output, $path, $commands)
    {
        $composer = $this->findComposer();

        $commands = array_map(function ($value) use ($composer) {
            return $composer . ' ' . $value;
        }, $commands);

        if ($input->getOption('no-ansi')) {
            $commands = array_map(function ($value) {
                return $value . ' --no-ansi';
            }, $commands);
        }
        $process = new Process(implode(' && ', $commands), $path, null, null, null);
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }
        return 'composer';
    }

}
