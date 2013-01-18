<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Common functions
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-17
 * @link   http://www.gracecode.com/
 */

require_once __DIR__ . "/config.inc.php";

$Database = new PDO("sqlite:" . CONFIG_DATABASE);

function get_data_from_url($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, defined("CONFIG_TIMEOUT") ? CONFIG_TIMEOUT : 50);
    curl_setopt($ch, CURLOPT_USERAGENT, 
        defined("CONFIG_USERAGENT") ? CONFIG_USERAGENT : "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)");
    curl_setopt($ch, CURLOPT_REFERER, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}


