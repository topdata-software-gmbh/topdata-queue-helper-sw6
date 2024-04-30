<?php

namespace Topdata\TopdataQueueHelperSW6\Util;

/**
 * utility functions for handling associative arrays (aka dicts)
 *
 * 12/2019
 */
class UtilDict
{


    /**
     *  Block list
     *    Remove the values you don't want
     *    var result = _.omit(credentials, ['age']);
     *
     * 02/2023 created (TopData)
     *
     * @param $dict
     * @param string[] $blockList list of "blocked" dict keys (blacklist)
     * @return array
     */
    public static function omit($dict, array $blockList): array
    {
        $ret = [];
        foreach ($dict as $key => $value) {
            if (!in_array($key, $blockList))
                $ret[$key] = $value;
        }

        return $ret;
    }


    private static function _pick(array $src, array &$dest, array $allowList)
    {
        foreach ($allowList as $path) {

            if (str_contains($path, '.')) {
                // ---- with sub keys
                [$mainKey, $remainingKey] = explode('.', $path, 2);
                if (!array_key_exists($mainKey, $src)) {
                    continue;
                }
                // ---- pick recursively
                $picked = [];
                self::_pick($src[$mainKey], $picked, [$remainingKey]);
                if (!empty($picked)) {
                    if (array_key_exists($mainKey, $dest) && is_array($dest[$mainKey])) {
                        $dest[$mainKey] = array_merge_recursive($dest[$mainKey], $picked);
                        // UtilDebug::d("------------------------------------------------", $picked, $dest);
                    } else {
                        $dest[$mainKey] = $picked;
                    }
                }
            } else {
                // ---- without sub keys
                if (!array_key_exists($path, $src)) {
                    continue;
                }
                if (array_key_exists($path, $dest) && is_array($dest[$path]) && is_array($src[$path])) {
                    // merge 2 arrays
                    $dest[$path] = array_merge_recursive($dest[$path], $src[$path]);
                } else {
                    $dest[$path] = $src[$path];
                }
            }

        }
    }

    /**
     * Allow list
     *   Only allow certain values
     *   var result = _.pick(credentials, ['fname', 'lname']);
     *
     * 02/2023 created (TopData)
     * 09/2023 now supports sub-keys (separated by dot), eg 'customFields.TopdataQueueHelperSW6_phone'
     *
     * @param array $src
     * @param string[] $allowList list of allowed dict keys (whitelist)
     * @return void
     */
    public static function pick(array $src, array $allowList): array
    {
        $dest = [];
        self::_pick($src, $dest, $allowList);

        return $dest;
    }


}
