<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Read Database Status
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-22
 * @link   http://www.gracecode.com/
 */

class DumpAreas extends Base {
    function __construct() {
        parent::__construct();
    }

    public function run() { }

    public function get() {
        $last_fetch_date = $this->getLastFetchDate();
        $last_record_date = $this->getLastRecordDate();
        $total_record = $this->getTotalRecordCount();

        return array("
            'last_fetch_date' => $last_fetch_date,
            'last_record_date' => $last_record_date,
            'total_record' => $total_record
            ");
    }
}

