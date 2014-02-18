<?php

/**
 * Twenty Questions osu! API
 * 
 * @author      GrygrFlzr
 * @copyright   (c) Martin Krisnanto Putra
 */

function osuStat($name) {
    //Build request
    $context = stream_context_create(array('http' => array(
        'method' => 'GET',
        'header' => 'User-Agent: TwentyQuestions IRC Bot' . "\r\n" .
                    'Accept: application/json'
    )));

    //Request user data
    $user_data = json_decode(file_get_contents(OSU_APIROOT . 
            'get_user?' .
            http_build_query(array(
                'k'    => OSU_APIKEY,
                'u'    => $name,
                'm'    => '0',
                'type' => 'string'
            ))
            , false, $context), true);
    
    return $user_data;
}

function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/**
 * isEmptyObject
 * Finds whether a variable is an empty object
 * @param mixed $var <p>
 * The variable being evaluated.
 * </p>
 * @return bool <b>TRUE</b> if <i>var</i> is an empty object,
 * <b>FALSE</b> otherwise.
 */
function isEmptyObject($var) {
    return (is_object($var) && (count(get_object_vars($var)) > 0));
}