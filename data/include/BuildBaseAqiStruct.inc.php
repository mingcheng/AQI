<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Build Base Structs
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-18
 * @link   http://www.gracecode.com/
 */

class BuildBaseAqiStruct extends Base {
    protected $data;

    function __construct() {
        parent::__construct();
    }

    public function run() {
        $this->buildTableStructs();
        $this->buildDatabaseIndex();
    }


    private function buildTableStructs() {
        $this->log("[struct] begin build aqi database struct");
        $query = "CREATE TABLE IF NOT EXISTS aqi (
            ID INTEGER NOT NULL PRIMARY KEY,
            division UNSIGNED BIG INT(10) NOT NULL,            
            areaName VARCHAR(12) DEFAULT NULL,
            value INTEGER NOT NULL,
            pollutant INTEGER DEFAULT NULL,
            recordDate DATE NOT NULL,
            _fetchDate DATE NOT NULL,
            source VARCHAR(8) DEFAULT NULL
        )";
        $this->Database->exec($query);

        $query = "CREATE TABLE IF NOT EXISTS pollutant (
            ID INTEGER NOT NULL PRIMARY KEY,
            name VARCHAR(32) NOT NULL
        )";
        $this->Database->exec($query);
        $this->log("...finished\n");
    }


    private function buildDatabaseIndex() {
        $this->log("[struct] begin build struct database index");
        $create_idx = array(
            "CREATE INDEX aqiRecordTimeIdx ON aqi(recordDate)",
            "CREATE INDEX aqiDivisionIdx ON aqi(division)",
            "CREATE INDEX aqiAreaNameIdx ON aqi(areaName)",
            "CREATE INDEX aqiPollutantNameIdx ON pollutant(name)"
        );

        foreach($create_idx as $query) {
            $this->Database->exec($query);
        }
        $this->log("...finished\n");
    }
}
