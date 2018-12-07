<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Coordinates(PATH . 'input');
define('MAXIMUM_DISTANCE', 10000);

$coords = [];
$index = 0;

$xMin = 999999;
$yMin = 999999;
$xMax = 0;
$yMax = 0;

while (($points = $reader->read()) !== false) {
    $index++;

    //$x, $y
    //grab the points and amend the min/max x/y values to help start the search
    extract($points);
    $xMin = min($x, $xMin);
    $yMin = min($y, $yMin);
    $xMax = max($x, $xMax);
    $yMax = max($y, $yMax);

    //store the coordinates for the search
    $coords["X{$index}"] = $points;
}

//work out the center, its the best place to start the search
$centerX = floor(($xMax - $xMin) / 2 + $xMin);
$centerY = floor(($yMax - $yMin) / 2 + $yMin);

/**
 * each iteration we'll step out by 1 in each direction, essentially iterating over squares
 *
 * 33333
 * 32223
 * 32123
 * 32223
 * 33333
 */
$stepOut = 0;
$RESULT = 0;

do {
    $foundWithinMinimumDistance = false;

    //update the min and max x/y values for this each
    $thisMinX = $centerX - $stepOut;
    $thisMinY = $centerY - $stepOut;
    $thisMaxX = $centerX + $stepOut;
    $thisMaxY = $centerY + $stepOut;

    /**
     * to avoid counting values twice we're going to do a search line this:
     *
     * xxx
     * y y
     * xxx
     *
     */
    for ($x = $thisMinX; $x <= $thisMaxX; $x++) {

        //we can't join the ifs together because it will only count once for 2 valids

        if (isWithinAllPoints($x, $thisMinY, $coords)) {
            $RESULT++;
            $foundWithinMinimumDistance = true;
        }

        //on the very center square, minY = maxY which will cause it to count twice
        if (isWithinAllPoints($x, $thisMaxY, $coords) && $thisMinY != $thisMaxY) {
            $RESULT++;
            $foundWithinMinimumDistance = true;
        }
    }

    //see comment higher, we dont want to count the very bottom/top Y as its already counted with the X
    for ($y = $thisMinY + 1; $y < $thisMaxY; $y++) {
        if (isWithinAllPoints($thisMinX, $y, $coords)) {
            $RESULT++;
            $foundWithinMinimumDistance = true;
        }
        if (isWithinAllPoints($thisMaxX, $y, $coords)) {
            $RESULT++;
            $foundWithinMinimumDistance = true;
        }
    }

    $stepOut++;
} while ($foundWithinMinimumDistance);


function isWithinAllPoints($x, $y, $points)
{
    $distance = 0;

    foreach ($points as $coord) {
        $distance += distanceBetween($x, $y, $coord['x'], $coord['y']);

        if ($distance >= MAXIMUM_DISTANCE) {
            return false;
        }
    }

    return true;
}

function distanceBetween($x1, $y1, $x2, $y2)
{
    $x = $x1 - $x2;
    $y = $y1 - $y2;
    return (max($x, -$x)) + (max($y, -$y));
}
