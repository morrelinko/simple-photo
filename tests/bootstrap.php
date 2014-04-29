<?php

require_once __DIR__ . "/../vendor/autoload.php";
$testDir = dirname(__FILE__);
foreach (array('files', 'files/default', 'files/sample', 'files/tmp') as $dir) {
    if (!is_dir($testDir . '/' . $dir)) {
        mkdir($testDir . '/' . $dir);
    }

}

copy($testDir . '/sample.png', $testDir . '/files/sample/sample.png');
