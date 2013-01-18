<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Common functions
 *
 * @author mingcheng<lucky#gracecode.com>
 * @date   2013-01-17
 * @link   http://www.gracecode.com/
 */

require_once __DIR__ . "/config.inc.php";

function __autoload($class_name) {
    $include_dirs = array(
        __DIR__ . '/helper',
        __DIR__ . '/include',
        __DIR__ . '/fetcher'
    );

    foreach($include_dirs as $dir) {
        $include_file = realpath($dir) . DIRECTORY_SEPARATOR . $class_name . '.inc.php';
        if (file_exists($include_file)) {
            return include_once $include_file;
        }
    }
}

