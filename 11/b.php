<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
define('SERIAL', 8199);
$RESULT = false;

$preGrid = [];
for ($y = 1; $y <= 300; $y++) {
    $preGrid[$y] = [];
    for ($x = 1; $x <= 300; $x++) {
        $preGrid[$y][$x] = GridSearch::calculate($x, $y);
    }
}

$gridSearch = new GridSearch(3, $preGrid);
for ($w = 1; $w < 301; $w++) {
    $gridSearch->w = $w;

    for ($x = 1; $x < 301-$w; $x++) {
        $gridSearch->x = $x;
        $gridSearch->y = 1;
        $gridSearch->calculateInitial();

        for ($y = 1; $y < 301-$w; $y++) {
            if (!$RESULT || $RESULT->sum < $gridSearch->sum) {
                $RESULT = clone $gridSearch;
            }
            $gridSearch->moveDown();
        }
    }
}


class GridSearch
{
    public $x;
    public $y;
    public $w;
    public $grid;

    public $sum = 0;

    public function __construct($size, &$grid)
    {
        $this->x = 1;
        $this->y = 1;
        $this->w = $size;
        $this->grid = &$grid;
    }

    public static function calculate($x, $y)
    {
        $rackId = $x + 10;
        $value = ($rackId * $y + SERIAL) * $rackId;
        $value = (floor($value / 100) % 10) - 5;
        return $value;
    }

    public function __toString()
    {
        return "{$this->x},{$this->y},{$this->w} ({$this->sum})";
    }

    public function calculateInitial()
    {
        $this->sum = 0;

        for ($y = $this->y; $y <= $this->y + $this->w - 1; $y++) {
            $slice = array_slice($this->grid[$y], $this->x - 1, $this->w);
            $this->sum += array_sum($slice);
        }
    }

    public function debug()
    {
        $this->sum = 0;

        for ($y = $this->y; $y <= $this->y + $this->w - 1; $y++) {
            $slice = array_slice($this->grid[$y], $this->x - 1, $this->w);
            $this->sum += array_sum($slice);
        }
    }

    public function moveDown()
    {
        $this->sum -= array_sum(array_slice($this->grid[$this->y], $this->x - 1, $this->w));
        $this->sum += array_sum(array_slice($this->grid[$this->y + $this->w], $this->x - 1, $this->w));
        $this->y++;
    }

    public function printGridAround($x, $y)
    {
        for ($j = $y - 1; $j < $y + 5; $j++) {
            if ($j == $y - 1) {
                for ($k = $x - 2; $k < $x + 5; $k++) {
                    echo str_pad($k, 3, ' ', STR_PAD_LEFT);
                }
                echo PHP_EOL;
            }
            for ($k = $x - 1; $k < $x + 5; $k++) {
                if ($k == $x - 1) {
                    echo str_pad($j, 3, ' ', STR_PAD_LEFT);
                }
                echo str_pad($this->grid[$j][$k], 3, ' ', STR_PAD_LEFT);
            }
            echo PHP_EOL;
        }
    }
}