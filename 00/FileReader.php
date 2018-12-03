<?php


abstract class FileReader
{
    protected $file = false;

    public function __construct($path)
    {
        $this->fileExists($path);
        $this->file = fopen($path, 'r');
    }

    protected function fileExists($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException('File Not Found: ' . $path);
        }
    }

    public function rewind()
    {
        if ($this->file) {
            rewind($this->file);
        }
    }

    abstract public function read($args = []);
}