<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Base Class
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-18
 * @link   http://www.gracecode.com/
 */


abstract class Base {
    const CONFIG_USERAGENT = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)";
    const CONFIG_TIMEOUT = 100;

    protected $Database;
    protected $LogFile;

    function __construct() {
        mb_internal_encoding("UTF-8");
        mb_regex_encoding("UTF-8");

        $this->Database = new PDO("sqlite:" . CONFIG_DATABASE);
        $log_file_name = CONFIG_DIR_LOGS . "/" . date("Y-m-d") . ".log";
        $this->LogFile = fopen($log_file_name, "a+");
    }


    protected function getDateFromUrl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONFIG_TIMEOUT);
        curl_setopt($ch, CURLOPT_USERAGENT, self::CONFIG_USERAGENT);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    public function getLastRecordDate($source = null) {

        $query = "SELECT max(recordDate) as max FROM aqi " . 
            ($source ? " WHERE source = '$source' " : "") ." LIMIT 1";

        foreach($this->Database->query($query) as $row) {
            return $row['max'];
        }

        return 0;
    }


    public function getTotalRecordCount() {
        $query = "SELECT count(ID) as count FROM aqi LIMIT 1";

        foreach($this->Database->query($query) as $row) {
            return $row['count'];
        }

        return 0;
    }

    
    public function getLastFetchDate($source = null) {
        $query = "SELECT max(_fetchDate) as max FROM aqi " . 
            ($source ? " WHERE source = '$source' " : "") . " LIMIT 1";

        foreach($this->Database->query($query) as $row) {
            return $row['max'];
        }

        return 0;
    }


    public function getDivisionId($area_name) {
        $query = "SELECT division FROM areas WHERE name LIKE '%$area_name%' LIMIT 1";
        foreach($this->Database->query($query) as $row) {
            return $row['division'];
        }

        return null;
    }


    public function isRecordExists($record_date, $division, $source) {
        $query = sprintf("SELECT ID FROM aqi WHERE recordDate = '%d' AND division = '%d' AND source = '%s'", 
            $record_date, $division, $source);

        foreach($this->Database->query($query) as $row) {
            return $row['ID'];
        }

        return false;
    }


    public function getPollutantId($pollutant_name) {
        $pollutant_id = null;
        $query = "SELECT ID FROM pollutant WHERE name LIKE '%$pollutant_name%' LIMIT 1";
        foreach($this->Database->query($query) as $row) {
            return $row['ID'];
        }

        $query = "INSERT INTO pollutant(name) VALUES ('$pollutant_name')";
        $results = $this->Database->exec($query);
        if (!$results) {
            echo "[database] Error! pollutant not updated.\n";
        }

        return $this->Database->lastInsertId();
    }


    public function insertAqiData($division, $value, $record_date, $pollutant = "", $area_name = "", $source = "") {
        if ($this->isRecordExists($record_date, $division, $source)) {
            $this->log(sprintf("Record with name which is %s(%s) is exists, ignore.\n",
                $area_name, date('Y-m-d', $record_date)));
            return;
        }

        $fetchDate = time();
        $pollutant_id = null;
        if (mb_strlen($pollutant, "UTF-8") > 2) {
            $pollutant_id = $this->getPollutantId($pollutant);
        }

        $query = "INSERT INTO aqi
            (division, value, recordDate, pollutant, areaName, source, _fetchDate) 
            VALUES 
            ('$division', '$value', '$record_date', '$pollutant_id', '$area_name', '$source', '$fetchDate')";

        $results = $this->Database->exec($query);
        if (!$results) {
            echo "Error! aqi record not insert.\n";
        }

        return $this->Database->lastInsertId();
    }


    protected function log($message) {
        fputs($this->LogFile, date('r') . " " . $message);
        echo $message;
    }


    abstract public function run();  

    function __destruct() {
        fclose($this->LogFile);
        $this->Database = null;
    }
}

