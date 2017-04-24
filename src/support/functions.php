<?php

namespace winwin\pay\sdk\support;

/**
 * Make snake-case strings.
 *
 * <code>
 *    echo snake_case('CocoBongo'); // coco_bongo
 *    echo snake_case('CocoBongo', '-'); // coco-bongo
 * </code>
 */
function snake_case($str, $delimiter = null)
{
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }

    return implode($delimiter === null ? '_' : $delimiter, $ret);
}
