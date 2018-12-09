<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
$input = explode(' ', $reader->read());

new Day08A($input, $RESULT);

class Day08A
{

    public $metas = [];
    public $map = [];

    public $RESULT;

    public function __construct($input, &$RESULT)
    {
        $this->result = &$RESULT;
        $this->recurse($input);
        $RESULT = array_sum($this->metas);
    }

    public function recurse($input)
    {
        $children = array_shift($input);
        $meta = array_shift($input);

        if ($children > 0) {
            for ($i = 0; $i < $children; $i++) {
                $input = $this->recurse($input);
            }
        }

        for ($i = 0; $i < $meta; $i++) {
            $this->metas[] = array_shift($input);
        }

        return $input;
    }

}


