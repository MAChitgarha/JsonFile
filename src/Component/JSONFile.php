<?php

namespace MAChitgarha\Component;

class JSONFile extends JSON
{
    protected $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        parent::__construct($this->read());
    }

    protected function read()
    {
        if (!file_exists($this->filePath))
            throw new \Exception("File doesn't exist");
        if (!is_readable($this->filePath))
            throw new \Exception("File is not readable");
        
        return file_get_contents($this->filePath);
    }

    public function save(int $options = JSON_PRETTY_PRINT)
    {
        file_put_contents($this->getDataAsJson($options));
    }
}
