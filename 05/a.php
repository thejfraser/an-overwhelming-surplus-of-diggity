<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');

$RESULT = $reader->read();
$regex = '';
foreach (range('a', 'z') as $letter) {
    $upper = strtoupper($letter);
    $regex .= sprintf('|%1$s%2$s|%2$s%1$s', $letter, $upper);
}
$regex = substr($regex, 1);
$regex = "/({$regex})/";
$matches = 0;

//preg_replace_callback is so slow, causes a 5 second load on this
//an alternative solution was provided that uses str_replace, its a LOT faster.
do {
    $matches = 0;
    $RESULT = preg_replace_callback($regex, function ($thisMatch) use (&$matches) {
        $matches++;
        return '';
    }, $RESULT);
} while ($matches > 0);

$RESULT = strlen(trim($RESULT));