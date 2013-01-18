<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Record location into single sqlite database.
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-17
 * @link   http://www.gracecode.com/
 */

class DumpAreas extends Base {
    private $PinYinEngine;
    protected $data;

    function __construct() {
        parent::__construct();

        $this->PinYinEngine = new PinYinEngine();
        $this->data = json_decode(file_get_contents(__DIR__."/../../assets/areas.json"));
    }

    public function run() {
        $this->buildTableStructs();

        $this->log("[area] begin build area data");
        foreach($this->data as $data) {
            $this->insertArea($data);
        }
        $this->log("...finished\n");

        $this->buildDatabaseIndex();
    }


    private function buildTableStructs() {
        $query = "CREATE TABLE IF NOT EXISTS areas (
            ID INTEGER NOT NULL PRIMARY KEY,
            division UNSIGNED BIG INT(10) NOT NULL,            
            name VARCHAR(12) NOT NULL,
            engName VARCHAR(64),
            pinyinName VARCHAR(64),
            bottom BOOLEAN DEFAULT FALSE,
            superior UNSIGNED BIG INT(10)
        )";

        return $this->Database->exec($query);
    }


    private function buildDatabaseIndex() {
        $this->log("[area] begin build area database index");
        $create_idx = array(
            "CREATE INDEX areaDivisionIdx ON areas(division)",
            "CREATE INDEX areaNameIdx ON areas(name)",
            "CREATE INDEX areaSuperiorIdx ON areas(surerior)"
        );

        foreach($create_idx as $query) {
            $this->Database->exec($query);
        }
        $this->log("...finished\n");
    }


    // area data from json file
    // build table structs
    private function insertArea($data, $superior = 0) {
        $division = $data->key;
        $name = $data->label;

        $pinyinName = $this->PinYinEngine->getAllPY($name);
        $query = "INSERT INTO areas(division, name, pinyinName, superior) 
            VALUES 
            ('$division', '$name', '$pinyinName', '$superior')";

        $results = $this->Database->exec($query);
        if (!$results) {
            $this->log("[error] database not updated");
        }

        if (isset($data->children)) {
            foreach($data->children as $items) {
                $this->insertArea($items, $division);
            }
        } else {
            $query = "UPDATE areas SET bottom = 1 WHERE division='$division'";
            $this->Database->exec($query);
        }
    }
}
