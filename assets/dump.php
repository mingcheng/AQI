<?php

define("DATABASE_FILE", "aqi.sqlite");

$Database = new PDO("sqlite:" . DATABASE_FILE);

///*
$sql = "SELECT * FROM aqi ORDER BY recordDate";

$sth = $Database->prepare($sql);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$fp = fopen('aqi.csv', 'w');
// http://www.fenanr.com/diy/111413.html
fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));

if (sizeof($result)) {
    foreach($result as $r) {
        $r['recordDate'] = date("Y-m-d", $r['recordDate']);
        unset($r['_fetchDate']);
        unset($r['source']);
        fputcsv($fp, $r);
    }
}
// */

//*
$sql = "SELECT * FROM areas";
$fp = fopen('areas.csv', 'w');

// http://www.fenanr.com/diy/111413.html
fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));

$sth = $Database->prepare($sql);
$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_ASSOC);

if (sizeof($result)) {
    foreach($result as $r) {
        fputcsv($fp, $r);
    }
}
// */

