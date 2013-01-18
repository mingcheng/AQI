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


function get_division_id($area_name) {
    global $Database;
    $query = "SELECT division FROM areas WHERE name LIKE '%$area_name%' LIMIT 1";
    foreach($Database->query($query) as $row) {
        return $row['division'];
    }

    return null;
}


function get_pollutant_id($pollutant_name) {
    global $Database;
    $pollutant_id = null;
    $query = "SELECT ID FROM pollutant WHERE name LIKE '%$pollutant_name%' LIMIT 1";
    foreach($Database->query($query) as $row) {
        return $row['ID'];
    }

    $query = "INSERT INTO pollutant(name) VALUES ('$pollutant_name')";
    $results = $Database->exec($query);
    if (!$results) {
        echo "[database] Error! pollutant not updated.\n";
    }

    return $Database->lastInsertId();
}


function insert_aqi_data_into_database($division, $value, $record_date, $pollutant = "", $area_name = "", $source = "") {
    global $Database;

    $fetchDate = time();
    $pollutant_id = null;
    if (strlen($pollutant) > 2) {
        $pollutant_id = get_pollutant_id($pollutant);
    }

    $query = "INSERT INTO aqi
        (division, value, recordDate, pollutant, areaName, source, _fetchDate) 
        VALUES 
        ('$division', '$value', '$record_date', '$pollutant_id', '$area_name', '$source', '$fetchDate')";

    $results = $Database->exec($query);
    if (!$results) {
        echo "[database] Error! aqi record not insert.\n";
    }

    return $Database->lastInsertId();
}

