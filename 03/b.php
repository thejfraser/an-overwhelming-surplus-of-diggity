<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');


$REGEX = '/\#([0-9]+) . ([0-9]+),([0-9]+)\: ([0-9]+)x([0-9]+)/';
$plots = [];
$claims = [];
while (($line = $reader->read()) != false) {

    $instr = [];
    preg_match($REGEX, $line, $instr);

    $claim = $instr[1];

    $x = $instr[2];
    $y = $instr[3];
    $w = $instr[4];
    $h = $instr[5];

    $claims[$claim] = [
        'x' => $x,
        'y' => $y,
        'w' => $w,
        'h' => $h
    ];

    for ($dy = $y; $dy < $y + $h; $dy++) {
        if (!isset($plots[$dy])) {
            $plots[$dy] = [];
        }

        for ($dx = $x; $dx < $x + $w; $dx++) {
            if (isset($plots[$dy][$dx])) {
                $plots[$dy][$dx] = '#';
                continue;
            }
            $plots[$dy][$dx] = $claim;
        }
    }
}

$invalidatedClaims = ['#' => 1];
foreach ($plots as $yRow) {
    foreach ($yRow as $xCol) {
        if (isset($invalidatedClaims[$xCol])) {
            continue;
        }

        echo 'checking claim on ' . $xCol . PHP_EOL;
        $claim = $claims[$xCol];

        $valid = true;
        for ($dy = $claim['y']; $dy < $claim['y'] + $claim['h']; $dy++) {
            for ($dx = $claim['x']; $dx < $claim['x'] + $claim['w']; $dx++) {
                if ($plots[$dy][$dx] == '#') {
                    $invalidatedClaims[$xCol] = 1;
                    $valid = false;
                    break;
                }
            }
            if (!$valid) {
                break;
            }
        }

        if ($valid) {
            $RESULT = $xCol;
            break 2;
        }
    }
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