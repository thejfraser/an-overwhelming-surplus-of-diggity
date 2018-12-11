<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_LRegex(PATH . 'input');
$points = [];
$i = 0;
$regex = '/position=<([- ]*[0-9]+),([- ]*[0-9]+)> velocity=<([- ]*[0-9]+), ([- ]*[0-9]+)>/';
while (($point = $reader->read(['regex' => $regex])) !== false) {
    $i++;
    list($string, $x, $y, $vX, $vY) = $point;
    $points[] = new Point($x, $y, $vX, $vY);
}

$reached = false;
//this took a bit of guess work in the first place
//basically I whittled it down by  using the distances, they started at 300, got the seconds, then reduced it in stages to get a more accurate seconds
$seconds = 10498;
while($seconds < 100) {
    $grid = [];
    $minX = 99999;
    $maxX = -99999;
    $minY = 99999;
    $maxY = -99999;
    foreach ($points as $point) {
        list($x, $y) = $point->after($seconds);

        $maxX = max($x, $maxX);
        $maxY = max($y, $maxY);
        $minX = min($x, $minX);
        $minY = min($y, $minY);
        if (!isset($grid[$y])) {
            $grid[$y] = [];
        }
        $grid[$y][$x] = '#';
    }

    $distX = $maxX - $minX;
    $distY = $maxY - $minY;
    //its rather hacky in hoping that the length of the word is less than 100 coords :D
    if ($distX < 100 && $distY < 50) {
        $reached = true;
        echo "seconds:" .  $seconds . PHP_EOL;
        for($y = $minY; $y <= $maxY; $y++) {
            if (!isset($grid[$y])) {
                echo PHP_EOL; continue;
            }
            for($x = $minX; $x <= $maxX; $x++) {
                echo isset($grid[$y][$x]) ? '##' : '  ';
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    } elseif ($reached) {
        exit;
    }
    $seconds++;
}
class Point {
    protected $startX = 0;
    protected $startY = 0;
    protected $velocityX = 0;
    protected $velocityY = 0;

    public function __construct($x, $y, $vX, $vY) {
        $this->startX = $x;
        $this->startY = $y;
        $this->velocityX = $vX;
        $this->velocityY = $vY;
    }

    public function after($seconds) {
        return [
            $this->startX + ($this->velocityX*$seconds),
            $this->startY + ($this->velocityY*$seconds),
        ];
    }
}