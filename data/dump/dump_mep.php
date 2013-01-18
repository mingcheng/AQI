<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Dump MEP Data into Database
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date
 * @link   http://www.gracecode.com/
 */

require_once __DIR__ . "/../config.inc.php";
require_once __DIR__ . "/../common.inc.php";

global $Database;

define("MAX_PAGE", 13000);

$result = array();
for ($page = MAX_PAGE; $page > 0; $page--) {
    $data_file_name = CONFIG_DIR_TEMP . "/" . $page . ".html";
    if (is_readable($data_file_name)) {
        $page_data = file_get_contents($data_file_name);

        preg_match_all('%<tr height=30 style="height:30px;">([\s\S]+?)</tr>%', $page_data, $matches);
        for($i = 2, $len = sizeof($matches[1]); $i < $len; $i++) {
            $data = trim(strip_tags($matches[1][$i]));
            $data = (split("\n", $data));
            if (sizeof($data) == 7) {
                $record_date = trim($data[2]);
                $record_date_time = strtotime($record_date);

                $area_name = trim($data[1]);
                $division_id = get_division_id($area_name);
                $value = trim($data[3]);

                $pollutant = trim($data[4]);
                $source = "mep";

                insert_aqi_data_into_database($division_id, $value, $record_date_time, $pollutant, $area_name, $source);
            }
        }
    }
}


