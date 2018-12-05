<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
$RESULT = $reader->read();

$letters = array_count_values(str_split(strtoupper($RESULT)));
ksort($letters);

$removedPolimers = $letters;
$letters = array_keys($letters);

//loop through each of the
foreach ($removedPolimers as $letter => $count) {

    //this string
    $string = str_replace([$letter, strtolower($letter)], '', $RESULT);
    do {
        $hasMatches = false;

        foreach ($letters as $upper) {
            if ($upper == $letter) {
                continue; //dont need to do this one
            }
            $lower = strtolower($upper);
            $thisMatches = 0;
            $string = str_replace([$upper . $lower, $lower . $upper], '', $string, $thisMatches);
            if ($thisMatches > 0) {
                $hasMatches = true;
            }
        }
    } while ($hasMatches);

    $removedPolimers[$letter] = strlen($string);
}
sort($removedPolimers);
$RESULT = $removedPolimers[0];