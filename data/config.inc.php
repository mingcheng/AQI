<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Configure files
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-17
 * @link   http://www.gracecode.com/
 */

date_default_timezone_set('Asia/Shanghai');

define("CONFIG_TIMEOUT", 100);
define("CONFIG_USERAGENT", "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)");
define("CONFIG_DATABASE", __DIR__ . "/../assets/aqi.sqlite");
define("CONFIG_DIR_TEMP", __DIR__ . "/tmp/");
define("CONFIG_DIR_LOGS", __DIR__ . "/logs/");


