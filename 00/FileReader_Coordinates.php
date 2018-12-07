<?php
require 'FileReader.php';

class FileReader_Coordinates extends FileReader
{
    public function read($args = [])
    {
        $line = fgetcsv($this->file);

        if (!$line) {
            return false;
        }

        return [
            'x' => trim($line[0]),
            'y' => trim($line[1]),
        ];
    }
}