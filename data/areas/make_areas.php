<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Record location into single sqlite database.
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-17
 * @link   http://www.gracecode.com/
 */

$sqlite_database = __DIR__ . "/../" . "areas.sqlite";
$loc = file_get_contents(__DIR__ . "/areas.json");
$loc_data = json_decode($loc);

$dbh = new PDO("sqlite:$sqlite_database");

$query = "CREATE TABLE IF NOT EXISTS areas (
            ID INTEGER NOT NULL PRIMARY KEY,
            division UNSIGNED BIG INT(10) NOT NULL,            
            name VARCHAR(12) NOT NULL,
            engName VARCHAR(64),
            bottom BOOLEAN DEFAULT FALSE,
            superior UNSIGNED BIG INT(10)
            )";
$results = $dbh->exec($query);

function insert_area($data, $superior = 0) {
    global $dbh;

    $division = $data->key;
    $name = $data->label;
    echo $name . "\n";

    $query = "INSERT INTO areas(division, name, superior) VALUES ('$division', '$name', '$superior')";
    $results = $dbh->exec($query);
    if (!$results) {
        echo "not updated";
    }

    if (isset($data->children)) {
        foreach($data->children as $items) {
            insert_area($items, $division);
        }
    } else {
        $query = "UPDATE areas SET bottom = 1 WHERE division='$division'";
        $dbh->exec($query);
    }
}


foreach($loc_data as $data) {
    insert_area($data);
}

$create_idx = array(
    "CREATE INDEX division_idx ON areas(division)",
    "CREATE INDEX name_idx ON areas(name)",
    "CREATE INDEX superior_idx ON areas(surerior)"
);

foreach($create_idx as $query) {
    $dbh->exec($query);
}

$dbh = null;

/*
id number
division number
name string
engName string
superior number
 */

