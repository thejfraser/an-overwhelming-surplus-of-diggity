<?php
/**
 * Whilst this worked for part a, it was too complex for part b.
 */
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';

$RESULT = 0;
$lastSquare;
for($y = 1; $y <= 297; $y++) {
    $gridSquare = new GridSquare(1, $y, 3, 3, 8199);

    for ($x = 1; $x <= 297; $x++) {
        $sum = $gridSquare->sum;
        if ($sum > $RESULT) {
            $RESULT = $sum;
            $lastSquare = clone $gridSquare;
        }
        $gridSquare->moveRight();
    }
}

$RESULT = $lastSquare->x .','. $lastSquare->y;

class GridSquare {
    public $points;

    public $x;
    public $y;
    public $w;
    public $h;
    public $serial;
    public $sum = 0;

    public function __construct($x, $y, $w, $h, $serial) {
        $this->x = $x;
        $this->y = $y;
        $this->w = $w;
        $this->h = $h;
        $this->serial = $serial;

        $row = array_fill($x, $w, 0);
        $points = array_fill($y, $h, $row);
        $this->points = $points;

        $this->calculateInitial();
    }

    protected function calculateInitial(){
        foreach($this->points as $y => $row) {
            foreach($row as $x => $value) {
                $this->points[$y][$x] = $this->calculate($x, $y);
            }
        }
        $this->sum();
    }

    public function calculate($x, $y) {
        $rackId = $x + 10;
        $value = ($rackId * $y + $this->serial) * $rackId;
        $value = (floor($value/100) % 10) - 5;
        return $value;
    }

    public function sum()
    {
        $sum = 0;
        foreach ($this->points as $row) {
            $sum+=array_sum($row);
        }
        $this->sum = $sum;
    }

    public function moveRight()
    {
        $this->x++;
        $key = $this->x+$this->w - 1;
        foreach($this->points as $y => &$row) {
            $row[] = $this->calculate($key, $y);
            array_shift($row);
        }
        $this->sum();
    }

    public function gridOut()
    {
        foreach($this->points as $row) {
            foreach ($row as $x) {
                echo str_pad($x, 3, ' ', STR_PAD_LEFT) . ' ';
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;echo PHP_EOL;
    }
}