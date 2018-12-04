<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Array(PATH . 'input');

$entries = $reader->read();
sort($entries);

$TIME_ACTION_REGEX = '/\[([0-9]{4})\-([0-9]{2})\-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\] (.*)/';

$minuteTracker = [];
$currentGuard = false;
$lastMinute = false;

foreach ($entries as $log) {
    $timeAction = [];
    preg_match($TIME_ACTION_REGEX, $log, $timeAction);
    list($log, $year, $month, $day, $hour, $minute, $action) = $timeAction;
    $minute = intval($minute);

    if (strtolower(substr($action, 0, 5)) == 'guard') {
        $found = [];
        preg_match('/guard #([0-9]+) /i', $action, $found);
        $currentGuard = $found[1];
        continue;
    }

    if ($action == 'falls asleep') {
        $lastMinute = $minute;
        continue;
    }

    if ($action == 'wakes up') {
        while ($lastMinute < $minute) {
            $key = $currentGuard . '_' . $lastMinute;
            if (!isset($minuteTracker[$key])) {
                $minuteTracker[$key] = 0;
            }
            $minuteTracker[$key]++;
            $lastMinute++;
            if ($lastMinute == 60) {
                $lastMinute = 0;
            }
        }
    }
}
//bring the key of the most slept minute to the top
arsort($minuteTracker);
//separate out the guard and the minute for multiplying
$key = explode('_', key($minuteTracker));
$RESULT = $key[0] * $key[1];
