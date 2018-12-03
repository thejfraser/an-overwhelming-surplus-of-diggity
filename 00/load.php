<?php

if (!defined('PATH')) {
    echo 'define PATH first' . PHP_EOL;
}
echo "\r\n";

function dd($var) {
    print_r($var);exit;
}

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$RESULT = false;
$TIME = microtime(true);
register_shutdown_function(function ($TIME) use (&$RESULT) {
    echo PHP_EOL . PHP_EOL;
    $TIME = microtime(true) - $TIME;
    echo "=================" . PHP_EOL;
    echo "RESULT: {$RESULT}" . PHP_EOL;
    echo "TIME: {$TIME}" . PHP_EOL;
    echo PHP_EOL . PHP_EOL;
}, $TIME);