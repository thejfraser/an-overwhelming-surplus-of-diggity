<?php
require 'FileReader.php';

class FileReader_LRegex extends FileReader
{
    public function read($args = ['regex' => ''])
    {
        $line = trim(fgets($this->file));
        if (!$line) {
            return false;
        }
        $matches = [];
        preg_match($args['regex'], $line, $matches);
        return $matches;
    }
}