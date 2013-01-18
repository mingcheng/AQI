<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Build base database
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-18
 * @link   http://www.gracecode.com/
 */

require_once __DIR__ . "/config.inc.php";
require_once __DIR__ . "/common.inc.php";

$builders = array('DumpAreas', 'BuildBaseAqiStruct', 'DumpDataFromMep');
foreach($builders as $builder) {
    $b = new $builder();
    $b->run();
}

