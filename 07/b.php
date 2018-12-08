<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
new Day07B($reader, $RESULT);

class Day07B
{
    const WORKER_COUNT = 5;
    const MIN_PROC_TIME = 60;

    public $processing = '';
    public $RESULT = '';

    protected $workers = [];
    protected $reader = false;
    protected $isBlocked = [];
    protected $queue = [];

    public function __construct(FileReader $reader, &$RESULT)
    {
        $this->reader = $reader;
        $this->RESULT = &$RESULT;

        $this->setupWorkers();
        $this->readIn();
        $this->prepQueue();
        $this->processQueue();
    }

    public function setupWorkers()
    {
        for ($i = 0; $i <= self::WORKER_COUNT; $i++) {
            $this->workers[] = [
                'W.ON' => false,
                'TTC'  => 0
            ];
        }
    }

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

    protected function addToBlocked($letterBlocked, $blockedBy = false)
    {
        if (!isset($this->isBlocked[$letterBlocked])) {
            $this->isBlocked[$letterBlocked] = [];
        }

        if (!$blockedBy) {
            return;
        }
        $this->isBlocked[$letterBlocked][] = $blockedBy;

    }

    protected function prepQueue()
    {
        //sort them A-Z then implode the strings
        ksort($this->isBlocked);
        foreach ($this->isBlocked as &$value) {
            $value = implode('', $value);
        }
    }

    public function processQueue()
    {
        $secondsPassed = -1;
        //iterate
        while (count($this->isBlocked) > 0 || $this->isWorking()) {

            $time = max(1, $this->timeUntilAvailableWorker());
            $this->passTime($time);
            $secondsPassed += $time;

            //process finished workers
            $this->processFinishedWorkers();

            if (count($this->isBlocked) > 0) {
                //assign new workers
                $this->assignNewWorkers();
            }
        }

        $this->RESULT = $secondsPassed;
    }

    public function isWorking()
    {
        foreach ($this->workers as $key => $worker) {
            if ($worker['TTC'] > 0) {
                return true;
            }
        }
        return false;
    }

    public function timeUntilAvailableWorker()
    {
        $minTimeUntilWorker = 0;
        foreach ($this->workers as $worker) {
            if ($worker['TTC'] === 0) {
                return 0;
            }

            $minTimeUntilWorker = min($minTimeUntilWorker, $worker['TTC']);
        }
        return $minTimeUntilWorker;
    }

    public function passTime($time)
    {
        foreach ($this->workers as &$worker) {
            $worker['TTC'] = max($worker['TTC'] - $time, 0);
        }
    }

    public function processFinishedWorkers()
    {
        foreach ($this->workers as &$worker) {
            if ($worker['TTC'] > 0 || !$worker['W.ON']) {
                continue;
            }

            $letter = $worker['W.ON'];
            foreach ($this->isBlocked as &$value) {
                $value = str_replace($letter, '', $value);
            }
            $worker['W.ON'] = false;
        }
    }

    public function assignNewWorkers()
    {
        do {
            $madeAssignment = false;
            $nextWorkerKey = $this->nextAvailableWorker();
            $nextUnblockedLetter = array_search('', $this->isBlocked);

            if ($nextUnblockedLetter && $nextWorkerKey !== false) {
                $this->removeFromBlocked($nextUnblockedLetter);
                $this->assignWorker($nextWorkerKey, $nextUnblockedLetter);
                $madeAssignment = true;

            }
        } while ($madeAssignment);
    }

    public function nextAvailableWorker()
    {
        foreach ($this->workers as $key => $worker) {
            if ($worker['W.ON'] === false) {
                return $key;
            }
        }
        return false;
    }

    protected function removeFromBlocked($letter)
    {
        unset($this->isBlocked[$letter]);
    }

    public function assignWorker($key, $letter)
    {
        $delay = ord($letter) - 64 + self::MIN_PROC_TIME;

        if ($this->workers[$key]['TTC'] > 0) {
            throw new RuntimeException('Attempting to assign a worker who is not finished');
        }

        $this->workers[$key]['W.ON'] = $letter;
        $this->workers[$key]['TTC'] = $delay;
    }

    protected function addToResult($letter)
    {
        $this->RESULT .= $letter;
    }
}