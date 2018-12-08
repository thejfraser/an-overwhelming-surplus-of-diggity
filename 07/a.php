<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
new Day07A($reader, $RESULT);

/**
 * Changed this solution to a class as there was a lot of passing variables during dev
 * Class Day07A
 */
class Day07A
{
    public $processing = '';
    public $RESULT = '';

    public $reader = false;
    public $isBlocked = [];
    public $queue = [];

    public function __construct(FileReader $reader, &$RESULT)
    {
        $this->reader = $reader;
        $this->RESULT = &$RESULT;

        $this->readIn();
        $this->prepQueue();
        $this->processQueue();
    }

    /**
     * Reads the input file and processes them to an isBlocked list
     */
    public function readIn()
    {
        while (($line = $this->reader->read()) != false) {
            $line = explode(" ", $line);
            $key = $line[1];
            $blocking = $line[7];

            $this->addToBlocked($key, false);
            $this->addToBlocked($blocking, $key);
        }
    }

    /**
     * Add the letter to the isBlocked list
     * @param $letterBlocked
     * @param bool $blockedBy
     */
    public function addToBlocked($letterBlocked, $blockedBy = false)
    {
        if (!isset($this->isBlocked[$letterBlocked])) {
            $this->isBlocked[$letterBlocked] = [];
        }

        //if the letter isnt blocked by (so its blocking be we initialise it anyway)
        if (!$blockedBy) {
            return;
        }
        $this->isBlocked[$letterBlocked][] = $blockedBy;

    }

    /**
     * Sort the blocked list in to A-Z order and then implode the blockers.
     */
    protected function prepQueue()
    {
        //sort them A-Z then implode the strings
        ksort($this->isBlocked);
        foreach ($this->isBlocked as &$value) {
            $value = implode('', $value);
        }
    }

    /**
     * Loop thorough until the isBlocked count is 0 and unblock
     */
    public function processQueue()
    {
        while (count($this->isBlocked) > 0) {
            $letter = array_search('', $this->isBlocked);
            $this->removeFromBlocked($letter);
            $this->unblockByLetter($letter);
            $this->addToResult($letter);
        }
    }

    public function removeFromBlocked($letter)
    {
        unset($this->isBlocked[$letter]);
    }

    public function unblockByLetter($letter)
    {
        foreach ($this->isBlocked as &$value) {
            $value = str_replace($letter, '', $value);
        }
    }

    public function addToResult($letter)
    {
        $this->RESULT .= $letter;
    }
}