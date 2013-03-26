<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-03-22
 */

require_once "common.inc.php";
require_once "config.inc.php";

$Database = new PDO("sqlite:".AQI_DATABASE);

$sql = 'select value, recordDate as date, areaName from aqi where division = %d order by recordDate';
$sql = sprintf($sql, getRequestParam("division", 330100, "get"));

$stmt = $Database->prepare($sql);
$stmt->execute();

$items = array();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//var_dump($result);

if (empty($result)) {
    $result[0]['areaName'] = "";
} else {
    foreach($result as $item) {
        array_push($items, 
            sprintf("[new Date(%s), %d]", date("Y, n, j", $item['date']), $item['value'])
        );
    }
}

header("Content-type: text/javascript;charset=utf-8");
printf('var data = [%s], areaName = "%s";', implode($items, ", "), $result[0]['areaName']);
$Database = null;

