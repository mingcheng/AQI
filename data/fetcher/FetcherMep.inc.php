<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Fetch Data from MEP - http://datacenter.mep.gov.cn/
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-19
 * @link   http://www.gracecode.com/
 */

class FetcherMep extends DumpDataFromMep {
    const FLAG_SOURCE = "mep";
    const URL_QUERY = "http://datacenter.mep.gov.cn/report/air_daily/air_dairy.jsp?city=&startdate=%s&enddate=%s&page=%d";
    const PREG_MATCH_TOTAL_PAGE = '%总页数：<b><font color="#004e98">(\d+)</font>%';
    const FORMAT_DATE = "Y-m-d";
    private $total_page = 1;
    private $last_record_date = 0;

    function __construct() {
        parent::__construct();

        $this->last_record_date = $this->getLastRecordDate(self::FLAG_SOURCE);
        $this->log('Last record data is ' . date('r', $this->last_record_date) . "\n");
    }

    private function setTotalPage() {
        $request =
            sprintf(self::URL_QUERY, 
                date(self::FORMAT_DATE, $this->last_record_date), date(self::FORMAT_DATE), $this->total_page);

        $page_data = $this->getDateFromUrl($request);
        preg_match(self::PREG_MATCH_TOTAL_PAGE, $page_data, $matches);
        if(isset($matches[1])) {
            $this->total_page = $matches[1];
        }

        $this->log('Total page is ' . $this->total_page . "\n");
    }


    private function recordDataFromPage($page_data) {
        $items = $this->matchAllItems($page_data);

        foreach($items as $item) {
            $last_insert_id = 
                $this->insertAqiData($item['division_id'], 
                $item['value'], $item['record_date'], $item['pollutant'], 
                $item['area_name'], self::FLAG_SOURCE);

            if ($last_insert_id) {
                $this->log(sprintf("The data which name is %s(%d) has been inserted into database.\n",
                    $item['area_name'], $last_insert_id));
            }
        }
    }


    public function run() {
        // set $this->total_page from page source
        $this->setTotalPage(); 

        for(;$this->total_page > 1; $this->total_page--) {
            $request =
                sprintf(self::URL_QUERY, 
                    date(self::FORMAT_DATE, $this->last_record_date), date(self::FORMAT_DATE), $this->total_page);

            $this->log($request . "\n");
            $this->log(sprintf("Total page remain %d\n", $this->total_page));
            $page_data = $this->getDateFromUrl($request);
            $this->recordDataFromPage($page_data);
        }
    }
}

