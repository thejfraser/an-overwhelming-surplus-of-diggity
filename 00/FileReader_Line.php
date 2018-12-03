<?php
require 'FileReader.php';

class FileReader_Line extends FileReader
{
    public function read($args = [])
    {
        return fgets($this->file);
    }
}