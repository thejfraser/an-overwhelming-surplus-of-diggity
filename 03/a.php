<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');


$REGEX = '/\#([0-9]+) . ([0-9]+),([0-9]+)\: ([0-9]+)x([0-9]+)/';
$plots = [];

while (($line = $reader->read()) != false) {

    $instr = [];
    preg_match($REGEX, $line, $instr);

    $claim = $instr[1];

    $x = $instr[2];
    $y = $instr[3];
    $w = $instr[4];
    $h = $instr[5];

    for ($dy = $y; $dy < $y + $h; $dy++) {
        if (!isset($plots[$dy])) {
            $plots[$dy] = [];
        }

        for ($dx = $x; $dx < $x + $w; $dx++) {
            if (!isset($plots[$dy][$dx])) {
                $plots[$dy][$dx] = 0;
            }
            $plots[$dy][$dx]++;
        }
    }
}


$RESULT = 0;
foreach ($plots as $yRow) {
    $yRow = array_filter($yRow, function ($value) {
        if ($value < 2) {
            return false;
        }

        return $value;
    });

    $RESULT += count($yRow);
}


function debug($plots)
{
    for ($y = 0; $y < 10; $y++) {
        for ($x = 0; $x < 10; $x++) {
            if (!isset($plots[$y]) || !isset($plots[$y][$x])) {
                echo '.';
                continue;
            }
            echo $plots[$y][$x] > 1 ? $plots[$y][$x] : '#';
        }
        echo PHP_EOL;
    }
}