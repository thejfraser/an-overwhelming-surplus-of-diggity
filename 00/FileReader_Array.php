<?php
require 'FileReader.php';

class FileReader_Array extends FileReader
{
    public function __construct($path)
    {
        $this->fileExists($path);
        $this->content = explode(PHP_EOL, file_get_contents($path));
        $this->content = array_filter($this->content, function ($value) {
            $value = trim($value);
            if (is_numeric($value)) {
                $value = floatval($value);
            }
            return $value;
        });
    }

    public function read($args = [])
    {
        return $this->content;
    }
}