<?php

namespace MAChitgarha\Component;

use Webmozart\PathUtil\Path;

class JSONFile extends JSON
{
    protected $filePath;
    protected $strictFile = false;
    protected $ignoreInvalidFile = false;

    const STRICT_FILE = 1;
    const IGNORE_INVALID_FILE = 2;

    public function __construct(string $filePath, int $options = 0)
    {
        $this->strictFile = $options & self::STRICT_FILE;
        $this->ignoreInvalidFile = $options & self::IGNORE_INVALID_FILE;

        $this->filePath = $filePath;

        try {
            $data = $this->read();
        } catch (\Exception $e) {
            if ($this->strictFile) {
                throw $e;
            } else {
                $this->create();
                $data = [];
            }
        }

        try {
            if ($data === "")
                $data = [];
            parent::__construct($data);
        } catch (\InvalidArgumentException $e) {
            if (!$this->ignoreInvalidFile)
                throw new \Exception("File does not contain a valid JSON");
            parent::__construct();
        }
    }

    protected function read()
    {
        if (!file_exists($this->filePath))
            throw new \Exception("File doesn't exist");
        if (!is_readable($this->filePath))
            throw new \Exception("File is not readable");
        
        return file_get_contents($this->filePath);
    }

    protected function write(string $data)
    {
        if (!file_exists($this->filePath))
            throw new \Exception("File doesn't exist");
        if (!is_writable($this->filePath))
            throw new \Exception("File is not readable");

        return @file_put_contents($this->filePath, $data);
    }

    public function save(int $options = JSON_PRETTY_PRINT)
    {
        $this->write($this->getDataAsJson($options));
    }

    protected function create()
    {
        if (!@touch($this->filePath))
            throw new \Exception("Cannot create the file");
        $this->write("[]");
        return true;
    }

    public function getFilename()
    {
        return Path::getFilename($this->filePath);
    }

    public function getFilePath()
    {
        return Path::canonicalize($this->filePath);
    }
}
