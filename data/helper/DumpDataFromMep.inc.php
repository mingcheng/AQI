<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Dump MEP Data into Database
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date
 * @link   http://www.gracecode.com/
 */

class DumpDataFromMep extends Base {
    const MAX_PAGE = 13000;
    const MATCH_PARTITION = '%<tr height=30 style="height:30px;">([\s\S]+?)</tr>%'; 

    function __construct() {
        parent::__construct();
    }


    public function matchAllItems($page_data) {
        $results = array();
        preg_match_all(self::MATCH_PARTITION, $page_data, $matches);

        if (isset($matches[1])) {
            for($i = 2, $len = sizeof($matches[1]); $i < $len; $i++) {
                $data = trim(strip_tags($matches[1][$i]));
                $data = preg_split("/\n/", $data);
                if (sizeof($data) >= 5) {
                    $org_id = intval($data[0]);
                    $area_name = preg_replace("/市$/", "", trim($data[1]));
                    $record_date = trim($data[2]);
                    $value = trim($data[3]);
                    $pollutant = trim($data[4]);
                    $source = trim(isset($data[5]) ? $data[5] : '');

                    $item = array(
                        'record_date' => strtotime($record_date),
                        'division_id' => $this->getDivisionId($area_name),
                        "area_name" => $area_name,
                        'pollutant' => $pollutant,
                        'value' => $value,
                        'source' => $source
                    );

                    array_push($results, $item);
                }
            }
        }

        return $results;
    }


    public function run() {
        for ($page = self::MAX_PAGE; $page > 0; $page--) {
            $data_file_name = CONFIG_DIR_TEMP . "/mep/" . $page . ".html";
            if (is_readable($data_file_name)) {
                $results = $this->matchAllItems(file_get_contents($data_file_name));

                foreach($results as $item) {
                    $division_id = $item['division_id'];
                    $value = $item['value'];
                    $record_date = $item['record_date'];
                    $pollutant = $item['pollutant'];
                    $area_name = $item['area_name'];
                    $source = $item['source'];

                    $last_insert_id = 
                        $this->insertAqiData($division_id, $value, $record_date, $pollutant, $area_name, $source);

                    if ($last_insert_id) {
                        $this->log("[dump]Dump data into database with id " . $last_insert_id . "\n");
                    }
                }
            }
        }

        $this->Database->exec("vacuum");
    }
}





