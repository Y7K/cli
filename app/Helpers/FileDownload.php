<?php
namespace App\Helpers;

class FileDownload
{

    public static function download($url, $auth = false, $progress = null)
    {
        $curl = curl_init();

        if($auth){
            curl_setopt($curl, CURLOPT_USERPWD, $auth);
        }

        $t_vers = curl_version();
        curl_setopt($curl, CURLOPT_USERAGENT, 'curl/' . $t_vers['version']);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

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

}
