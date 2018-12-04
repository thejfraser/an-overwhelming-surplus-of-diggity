<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Array(PATH . 'input');

$entries = $reader->read();
sort($entries);

$TIME_ACTION_REGEX = '/\[([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\] (.*)/';

$totalSleepingTime = [];
$minuteTracker = [];

$currentGuard = false;
$lastMinute = false;

foreach ($entries as $log) {
    $timeAction = [];
    preg_match($TIME_ACTION_REGEX, $log, $timeAction);
    list($log, $year, $month, $day, $hour, $minute, $action) = $timeAction;
    $minute = intval($minute); //so we dont have 04 for a minute

    //detect the guard working
    if (strtolower(substr($action, 0, 5)) == 'guard') {
        $found = [];
        preg_match('/guard #([0-9]+) /i', $action, $found);
        $currentGuard = $found[1];

        //initialise the trackers as needed
        if (!isset($minuteTracker[$currentGuard])) {
            $minuteTracker[$currentGuard] = [];
            $totalSleepingTime[$currentGuard] = 0;
        }
        continue;
    }

    //log the sleep start time
    if ($action == 'falls asleep') {
        $lastMinute = $minute;
        continue;
    }

    if ($action == 'wakes up') {
        //step through the minutes until he wakes up, this assumes he was sleeping for less than an hour
        while ($lastMinute < $minute) {
            //initialise the tracker
            if (!isset($minuteTracker[$currentGuard][$lastMinute])) {
                $minuteTracker[$currentGuard][$lastMinute] = 0;
            }
            //add times
            $minuteTracker[$currentGuard][$lastMinute]++;
            $totalSleepingTime[$currentGuard]++;
            $lastMinute++;
            //over hour loop check
            if ($lastMinute == 60) {
                $lastMinute = 0;
            }
        }
    }
}

//bring the guard with the most sleeping time to the top
arsort($totalSleepingTime);
$mostSleepingGuard = key($totalSleepingTime);

//bring the guard's most slept minute to the top.
arsort($minuteTracker[$mostSleepingGuard]);
$mostSleepingMinute = key($minuteTracker[$mostSleepingGuard]);

$RESULT = $mostSleepingGuard * $mostSleepingMinute;