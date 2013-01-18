<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Record location into single sqlite database.
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-17
 * @link   http://www.gracecode.com/
 */

require_once __DIR__ . "/../config.inc.php";
require_once __DIR__ . "/../common.inc.php";

global $Database;

// area data from json file
$loc = file_get_contents(__DIR__ . "/areas.json");
$loc_data = json_decode($loc);

// build table structs
$query = "CREATE TABLE IF NOT EXISTS areas (
            ID INTEGER NOT NULL PRIMARY KEY,
            division UNSIGNED BIG INT(10) NOT NULL,            
            name VARCHAR(12) NOT NULL,
            engName VARCHAR(64),
            bottom BOOLEAN DEFAULT FALSE,
            superior UNSIGNED BIG INT(10)
            )";
$Database->exec($query);

function insert_area($data, $superior = 0) {
    global $Database;

    $division = $data->key;
    $name = $data->label;

    $query = "INSERT INTO areas(division, name, superior) VALUES ('$division', '$name', '$superior')";
    $results = $Database->exec($query);
    if (!$results) {
        echo "not updated";
    }

    if (isset($data->children)) {
        foreach($data->children as $items) {
            insert_area($items, $division);
        }
    } else {
        $query = "UPDATE areas SET bottom = 1 WHERE division='$division'";
        $Database->exec($query);
    }
}

echo "[area] begin build area data";
foreach($loc_data as $data) {
    insert_area($data);
}
echo "...finished\n";

echo "[area] begin build area database index";
$create_idx = array(
    "CREATE INDEX areaDivisionIdx ON areas(division)",
    "CREATE INDEX areaNameIdx ON areas(name)",
    "CREATE INDEX areaSuperiorIdx ON areas(surerior)"
);

foreach($create_idx as $query) {
    $Database->exec($query);
}
echo "...finished\n";

