<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Array(PATH . 'input');
echo array_sum($reader->read());

