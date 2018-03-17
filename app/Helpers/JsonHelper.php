<?php
/**
 * Created by PhpStorm.
 * User: joris
 * Date: 16.03.18
 * Time: 18:37
 */

namespace App\Helpers;


class JsonHelper
{

    public static function mergeJsonFiles($originalFile, $fileToMerge)
    {
        $originalJson = is_file($originalFile) ? json_decode(file_get_contents($originalFile), true) : [];
        $newJson = is_file($fileToMerge) ? json_decode(file_get_contents($fileToMerge), true) : [];
        $mergedJson = self::mergeJsonArrays($originalJson, $newJson);

        unlink($fileToMerge);

        file_put_contents($originalFile, json_encode($mergedJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

    public static function mergeJsonArrays($priority_json, $merge_json)
    {
        foreach ($merge_json as $merge_content_key => $merge_content_value) {
            if (!array_key_exists($merge_content_key, $priority_json)) {
                $priority_json[$merge_content_key] = $merge_content_value;
            } elseif (!is_string($merge_content_value)) {
                $priority_json[$merge_content_key] = self::mergeJsonArrays($priority_json[$merge_content_key], $merge_content_value);
            } else {
                $value = is_array($merge_content_value) ? $merge_content_value : [$merge_content_value];
                $priority_json = array_merge($priority_json, $value);
            }
        }
        return $priority_json;
    }
}
