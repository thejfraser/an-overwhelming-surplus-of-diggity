<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
define('GENERATIONS', 2000); //part a

$initialState = $reader->read();
$initialState = trim(substr($initialState, strpos($initialState, ': ') + 2));

$spreads = Spreads::getInstance();
do {
    $spread = trim($reader->read());
    //there is sometimes one blank line between initial state and spreads
    if ($spread === "") {
        continue;
    }

    $spreads->addSpread(substr($spread, 0, 5), substr($spread, -1));
} while (!$reader->eof());

//now for the initial states
$collection = new Collection;

for ($i = 0; $i < strlen($initialState); $i++) {
    $collection->addPot($initialState[$i]);
}
$lastSum = $collection->sum();
$generationSumDiffs = [];
for ($i = 0; $i < GENERATIONS; $i++) {
    $collection->checkEnds();

    do {
        $collection->updatePot($spreads->tick($collection->visualise()));
        $hasNextPot = $collection->nextPot();
    } while ($hasNextPot);
    $collection->completeCycle();

    $thisSum = $collection->sum();
    $diff = $thisSum - $lastSum;
    $generationSumDiffs[$i] = $diff;
    if (count($generationSumDiffs) > 20) {
        array_shift($generationSumDiffs);
    }
    $lastSum = $thisSum;
}
//after GENERATIONS it averages 62
//now reddit told me that after GENERATIONS its always going to add 62.
$RESULT = ((50000000000 - $i) * 62) + $thisSum;

class Collection
{
    public $iteratedCycles = 0;
    protected $collection = [];
    protected $nextIteration = [];
    protected $internalCounter = 0;
    protected $potsPrepended = 0;

    public function checkEnds()
    {
        $firstPlant = array_search('#', $this->collection);
        $toRemove = $firstPlant - 2;
        if ($toRemove > 0) {
            $this->potsPrepended -= $toRemove;
            array_splice($this->collection, 0, $toRemove);
        }

        //we're cycling round to the beginning but check the end to see if we need to add empty pots.
        $this->internalCounter = count($this->collection) - 1;
        if (
            $this->current() == '#'
            ||
            $this->left() == '#'
            ||
            $this->leftLeft() == '#'
        ) {
            $this->addPot();
            $this->addPot();
        }

        $this->internalCounter = 0;
        //just in case, if any of the first 3 have plants, we need ot add those pots
        if (
            $this->current() == '#'
            ||
            $this->right() == '#'
            ||
            $this->rightRight() == '#'
        ) {
            $this->prependPot();
            $this->prependPot();
        }
    }

    public function current()
    {
        return $this->collection[$this->internalCounter];
    }

    public function left()
    {
        return $this->checkPot(-1);
    }

    protected function checkPot($dir)
    {
        $tempCounter = $this->internalCounter + $dir;
        if (isset($this->collection[$tempCounter])) {
            return $this->collection[$tempCounter];
        }

        return '.';
    }

    public function leftLeft()
    {
        return $this->checkPot(-2);
    }

    public function addPot($plant = '.')
    {
        $this->collection[] = $plant;
    }

    public function right()
    {
        return $this->checkPot(1);
    }

    public function rightRight()
    {
        return $this->checkPot(2);
    }

    public function prependPot($plant = '.')
    {
        $this->potsPrepended++;
        array_unshift($this->collection, $plant);
    }

    public function completeCycle()
    {
        if (count($this->nextIteration)) {
            $this->collection = $this->nextIteration;
            $this->iteratedCycles++;
            $this->nextIteration = [];
        }
    }

    public function updatePot($plant = '.')
    {
        return $this->nextIteration[$this->internalCounter] = $plant;
    }

    public function nextPot()
    {
        $this->internalCounter++;
        return isset($this->collection[$this->internalCounter]);
    }

    public function __toString()
    {
        return implode('', $this->collection) . PHP_EOL;
    }

    public function visualise()
    {
        $offset = $this->internalCounter - 2;
        $length = 5;
        if ($offset < 0) {
            $length = 5 + $offset;
        }
        $slice = implode('', array_slice($this->collection, max(0, $offset), $length));

        if (!isset($slice[4])) {
            $slice = str_pad($slice, 5, '.', $offset <= 0 ? STR_PAD_LEFT : STR_PAD_RIGHT);
        }

        return $slice;
    }

    public function visualiseB()
    {
        return $this->leftLeft() . $this->left() . $this->current() . $this->right() . $this->rightRight();
    }

    public function sum()
    {
        $sum = 0;
        foreach ($this->collection as $index => $pot) {
            if ($pot === '#') {
                $sum += ($index - $this->potsPrepended);
            }
        }
        return $sum;
    }

}

class Spreads
{
    static $instance = false;
    protected $hasPlantSpreads = [];
    protected $notHasPlantSpreads = [];

    protected function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            new static();
        }

        return static::$instance;
    }

    public function addSpread(string $spread, string $result)
    {
        $plant = $spread[2]; //the spread is 5, the middle references the current plant.
        if ($plant === '#') {
            $this->hasPlantSpreads[$spread] = $result;
        } else {
            $this->notHasPlantSpreads[$spread] = $result;
        }
    }

    public function tick($vis)
    {
        $center = $vis[2];
        if ($center == '#') {
            return isset($this->hasPlantSpreads[$vis]) ? $this->hasPlantSpreads[$vis] : '.';
        }
        return isset($this->notHasPlantSpreads[$vis]) ? $this->notHasPlantSpreads[$vis] : '.';
    }

    public function toArray()
    {
        return [
            0 => $this->notHasPlantSpreads,
            1 => $this->hasPlantSpreads,
        ];
    }
}