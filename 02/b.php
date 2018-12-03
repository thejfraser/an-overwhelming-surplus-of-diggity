<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Array(PATH . 'input');

$array = $reader->read();
sort($array);

$lastLine = false;

foreach ($array as $line) {

    if (!$lastLine) {
        $lastLine = $line;
        continue;
    }

    //returns the minimum number of letter changes needed to match
    //we want 1;
    $lev = levenshtein($lastLine, $line);

    if ($lev > 1) {
        $lastLine = $line;
        continue;
    }

    $RESULT = implode(array_intersect(str_split($lastLine), str_split($line)));
    break;
}
