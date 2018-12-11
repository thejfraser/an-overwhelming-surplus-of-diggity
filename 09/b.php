<?php
ini_set('zend.enable_gc', 0); //this was segfaulting;
ini_set('memory_limit', '1G'); //surely enough

define('MARBLE_MULTIPLIER', 1); //set to 100 for part b.

define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
$puzzleLine = $reader->read();
$parameters = [];
preg_match('/([0-9]+) players; last marble is worth ([0-9]+) points/', $puzzleLine, $parameters);
list($puzzleLine, $players, $marbles) = $parameters;

//set up the player scores.
$playerScores = array_fill(1, $players, '0');
$marbles *= MARBLE_MULTIPLIER;

//initial values
$player = 0;
$currentValue = 0;
$zeroMarble = new Marble(0);
$currentValue++;
$player++;


//the first couple of marbles are iffy
//first marble
$currentMarble = new Marble($currentValue);
$zeroMarble->previous = $currentMarble;
$zeroMarble->next = $currentMarble;
$currentMarble->previous = $zeroMarble;
$currentMarble->next = $zeroMarble;
$currentValue++;
$player++;

//second marble
$newMarble = new Marble($currentValue);
$newMarble->next = $currentMarble;
$newMarble->previous = $currentMarble->previous;
$currentMarble->previous->next = $newMarble;
$currentMarble->previous = $newMarble;
$currentMarble = $newMarble;


//3rd onwards should be okay
while ($currentValue <= $marbles) {
    $currentValue++;
    $player++;

    //prevent player overflow
    if ($player > $players) {
        $player = 1;
    }

    //23 is something special
    if ($currentValue % 23 === 0) {
        //step back 7
        $currentMarble = $currentMarble->previous->previous->previous->previous->previous->previous->previous;
        //remove this one
        $currentMarble->previous->next = $currentMarble->next;
        $currentMarble->next->previous = $currentMarble->previous;

        //score some points.
        $playerScores[$player] += $currentValue;
        $playerScores[$player] += $currentMarble->value;

        //move right one, losing the current forever
        $currentMarble = $currentMarble->next;
        continue;
    }

    //otherwise
    $newMarble = new Marble($currentValue);

    //set up the next and previous
    $newMarble->next = $currentMarble->next->next;
    $newMarble->previous = $currentMarble->next;

    //slide it in
    $currentMarble->next->next->previous = $newMarble;
    $currentMarble->next->next = $newMarble;
    //set it for current
    $currentMarble = $newMarble;
}

rsort($playerScores);
$RESULT = $playerScores[0];

class Marble
{
    public $value = 0;
    public $previous = false;
    public $next = false;

    public function __construct($value = 0)
    {
        $this->value = $value;
    }
}