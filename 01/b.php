<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');

$frequencies = [];
$frequency = 0;
$metDuplicate = false;

while (!$metDuplicate) {
    while (($line = $reader->read()) != false) {
        $frequency += $line;

        if (isset($frequencies[$frequency])) {
            $metDuplicate = true;
            break;
        }

        $frequencies[$frequency] = 1;
    }
    if (!$metDuplicate) {
        $reader->rewind();
    }
}
$RESULT = $frequency;
