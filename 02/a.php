<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');

$totalHasTwo = 0;
$totalHasThree = 0;

while (($line = $reader->read()) != false) {

    $line = array_count_values(str_split($line, 1));

    $totalHasTwo += (intval(array_search(2, $line) != false));
    $totalHasThree += (intval(array_search(3, $line) != false));
}

$RESULT = $totalHasThree * $totalHasTwo;