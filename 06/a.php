<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Coordinates(PATH . 'input');

$map = [];
$coords = [];
$areas = [];
$index = 0;

//everything outside of this would be infinite
$xMin = 999999;
$yMin = 999999;
$xMax = 0;
$yMax = 0;

//work though all the coordinates and map them
while (($points = $reader->read()) !== false) {
    $index++;
    $letter = "X{$index}_0";
    $areas["X{$index}"] = 0;

    //$x, $y
    extract($points);

    //used to calculate the infintes later on
    $xMin = min($x, $xMin);
    $yMin = min($y, $yMin);
    $xMax = max($x, $xMax);
    $yMax = max($y, $yMax);

    $coords[$letter] = $points;

    if (!isset($map[$y])) {
        $map[$y] = [];
    }

    if (!isset($map[$y][$x])) {
        $map[$y][$x] = '.';
    }

    $map[$y][$x] = $letter;
}

//work out the distances for all points within the grid of min-max
for ($y = $yMin; $y <= $yMax; $y++) {

    if (!isset($map[$y])) {
        $map[$y] = [];
    }

    for ($x = $xMin; $x <= $xMax; $x++) {

        if (!isset($map[$y][$x]) || $map[$y][$x] === '.' ) {
            $currentDistance = 999999;
            $from = false;
        } else {
            $square = explode('_', $map[$y][$x]);
            $currentDistance = $square[1];
            $from = $square[0];
        }

        if ($currentDistance == 0) {
            continue;
        }

        $thisPointDistances = [];

        foreach ($coords as $coord => $gridReference) {
            if ($from === $coord) {
                $thisPointDistances[$coord] = $currentDistance;
                continue; //no point doing this one
            }

            $thisDistance = distanceBetween($x, $y, $gridReference['x'], $gridReference['y']);
            $thisPointDistances[$coord] = $thisDistance;
        }

        asort($thisPointDistances);

        $nearestPoint = key($thisPointDistances);
        $nearestPointValue = $thisPointDistances[$nearestPoint];
        $pointValues = array_count_values($thisPointDistances);

        //this is a multiple, so we're making it so
        if ($pointValues[$nearestPointValue] > 1) {
            $map[$y][$x] = 'M';
            continue;
        }

        $nearestPoint = explode('_', $nearestPoint);
        $map[$y][$x] = $nearestPoint[0] . '_' . $nearestPointValue;
    }
}

//now we need to count the values;
//the first and last rows/cols are going to be infinite
for ($y = $yMin; $y <= $yMax; $y++) {

    $thisRow = $map[$y];

    unset($areas[toPoint($thisRow[$xMin])]);
    unset($areas[toPoint($thisRow[$xMax])]);

    array_walk($thisRow, function (&$value) {
        $value = toPoint($value);
    });
    $values = array_count_values($thisRow);

    if ($y === $yMin || $y === $yMax) {
        foreach ($values as $key => $count) {
            unset($areas[$key]);
        }
    } else {
        foreach ($values as $key => $count) {
            if (isset($areas[$key])) {
                $areas[$key] += $count;
            }
        }
    }
}
//sort the array to get the biggest area to the top.
arsort($areas);
$RESULT = array_shift($areas);

function toPoint($value)
{
    $value = explode('_', $value);
    return $value[0];
}

function distanceBetween($x1, $y1, $x2, $y2)
{
    $x = $x1 - $x2;
    $y = $y1 - $y2;
    return (max($x, -$x)) + (max($y, -$y));
}
