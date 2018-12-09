<?php
define('PATH', __DIR__ . '/');
require PATH . '../00/load.php';
$reader = new FileReader_Line(PATH . 'input');
$input = explode(' ', $reader->read());

new Day08B($input, $RESULT);

class Day08B
{
    public $map = [];

    public $RESULT;

    public function __construct($input, &$RESULT)
    {
        $this->result = &$RESULT;
        $result = $this->recurse($input);
        $RESULT = $result['value'];
    }

    public function recurse($input)
    {
        $children = array_shift($input);
        $meta = array_shift($input);

        if ($children == 0) {
            $value = 0;
            for ($i = 0; $i < $meta; $i++) {
                $value += array_shift($input);
            }

            return [
                'input' => $input,
                'value' => $value
            ];
        }

        $values = [];

        if ($children > 0) {
            for ($i = 0; $i < $children; $i++) {
                $tempInput = $this->recurse($input);
                $values[$i + 1] = $tempInput['value'];
                $input = $tempInput['input'];
            }
        }

        $value = 0;
        for ($i = 0; $i < $meta; $i++) {
            $thisMeta = array_shift($input);
            if (isset($values[$thisMeta])) {
                $value += $values[$thisMeta];
            }
        }

        return [
            'input' => $input,
            'value' => $value
        ];
    }

}


