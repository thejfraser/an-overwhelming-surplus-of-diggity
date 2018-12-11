<?php
/**
 * This is really slow, its n(n) complexity at the minimum, as such, no docs for you
 */
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');

$puzzleLine = $reader->read();
echo $puzzleLine . PHP_EOL;
$parameters = [];
preg_match('/([0-9]+) players; last marble is worth ([0-9]+) points/', $puzzleLine, $parameters);
list($puzzleLine, $players, $marbles) = $parameters;
new Day09A($players, $marbles, $RESULT);

class Day09A
{
    protected $currentMarble = 0;
    protected $currentPlayer = 0;
    protected $gameBoard = [0];
    protected $playerScores = [];
    protected $currentIndex = 0;

    protected $PLAYERS = 0;
    protected $MARBLES = 0;

    public function __construct($players, $marbles, &$RESULT)
    {
        $this->PLAYERS = $players;
        $this->setupPlayers();
        $this->MARBLES = $marbles;

        $this->play();

        rsort($this->playerScores);
        $RESULT = $this->playerScores[0];
    }

    protected function setupPlayers()
    {
        $this->playerScores = array_fill(1, $this->PLAYERS, '0');
    }

    public function play()
    {
        do {
            $this->nextTurn();
            if ($this->is23()) {
                continue;
            }
            $this->calculateNextPlacementSlot();
            $this->placeNextMarble();
        } while ($this->hasMarblesRemaining());
    }

    protected function nextTurn()
    {
        $this->currentMarble++;
        $this->currentPlayer++;
        if ($this->currentPlayer > $this->PLAYERS) {
            $this->currentPlayer = 1;
        }
    }

    protected function is23()
    {
        if (!($this->currentMarble % 23 == 0)) {
            return false;
        }

        $this->stepBack7();

        $score = $this->currentMarble + $this->gameBoard[$this->currentIndex];
        echo $score . PHP_EOL;

        if ($this->currentMarble > 230) {
            exit;
        }
        $this->currentPlayerScore($this->currentMarble);//player scores this.
        $this->currentPlayerScore($this->gameBoard[$this->currentIndex]); //player also scores this
        $this->removeAndRekey();
        return true;
    }

    protected function stepBack7()
    {
        $this->currentIndex -= 7;
        if ($this->currentIndex < 0) {
            $this->currentIndex = $this->countGameBoard() + $this->currentIndex;
        }
    }

    public function countGameBoard()
    {
        return count($this->gameBoard);
    }

    protected function currentPlayerScore($points)
    {
        $this->playerScores[$this->currentPlayer] += $points;
    }

    protected function removeAndRekey()
    {
        $nextIndex = $this->currentIndex + 1;
        if (!isset($this->gameBoard[$nextIndex])) {
            $nextIndex = 0;
        }
        $nextValue = $this->gameBoard[$nextIndex];
        unset($this->gameBoard[$this->currentIndex]);
        $this->gameBoard = array_values($this->gameBoard);
        $this->currentIndex = array_search($nextValue, $this->gameBoard);
    }

    protected function calculateNextPlacementSlot()
    {
        $this->currentIndex++;
        if ($this->currentIndex == $this->countGameBoard()) {
            $this->currentIndex = 0;
        }
        $this->currentIndex++;
        if ($this->currentIndex > $this->countGameBoard()) {
            $this->currentIndex = 0;
        }
        if ($this->currentIndex === 0) {
            $this->currentIndex += 1;
        }
    }

    protected function placeNextMarble()
    {
        if ($this->currentIndex === count($this->gameBoard)) {
            //the index is already on the marble just added
            $this->gameBoard[] = $this->currentMarble;
            return;
        }

        array_splice($this->gameBoard, $this->currentIndex, 0, $this->currentMarble);
    }

    protected function hasMarblesRemaining()
    {
        return $this->currentMarble <= $this->MARBLES;
    }


}


