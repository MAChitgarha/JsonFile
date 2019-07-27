<?php
/**
 * JsonFile class file.
 *
 * @author Mohammad Amin Chitgarha <machitgarha@outlook.com>
 * @see https://github.com/MAChitgarha/JsonFile
 * @see https://packagist.org/machitgarha/jsonfile
 */

namespace MAChitgarha\Component;

use Webmozart\PathUtil\Path;
use MAChitgarha\Json\Exception\Exception;
use MAChitgarha\Json\Exception\InvalidJsonException;
use MAChitgarha\JsonFile\Option\FileOpt;

/**
 * Handles JSON files.
 *
 * Reads a JSON file, make some operations on it and saves it.
 *
 * @see https://github.com/MAChitgarha/JsonFile/wiki
 */
class JsonFile extends Json
{
    /** @var string */
    protected $filePath;
    
    /** @var bool {@see JsonFile::MUST_EXIST} */
    protected $fileMustExist = false;

    /**
     * Reads the JSON file data.
     *
     * The file will be created if it does not exist.
     *
     * @param string $filePath File path to be read.
     * @param int $fileOptions A combination of FileOpt::* constants.
     * @param int $jsonOptions A combination of JsonOpt::* constants.
     * @throws InvalidJsonException If the file does not contain a valid JSON data.
     */
    public function __construct(string $filePath, int $fileOptions = 0, int $jsonOptions = 0)
    {
        // Setting options
        $this->fileMustExist = (bool)($fileOptions & FileOpt::MUST_EXIST);

        $this->filePath = $filePath;

        $data = "";
        // Read the file
        try {
            $data = $this->read();
            // The file doesn't exist
        } catch (Exception $e) {
            if ($this->fileMustExist) {
                throw $e;
            } else {
                $this->create();
            }
        }

        try {
            // If the file is empty, set data to an empty array
            if ($data === "") {
                $data = [];
            }
            parent::__construct($data, $jsonOptions);
            // The file doesn't contain an invalid JSON
        } catch (InvalidJsonException $e) {
            throw new InvalidJsonException("File does not contain a valid JSON");
        }
    }

    /**
     * Reads from the file.
     *
     * @return string File contents.
     * @throws Exception When the file doesn't exist.
     * @throws Exception When the file isn't readable (e.g. permission denied).
     */
    protected function read(): string
    {
        $filePath = $this->filePath;

        if (!file_exists($filePath)) {
            throw new Exception("File doesn't exist");
        }
        if (!is_readable($filePath)) {
            throw new Exception("File is not readable");
        }

        $file = new \SplFileObject($filePath, "r");
        $contents = $file->fread($file->getSize());

        if ($contents === false) {
            throw new Exception("Cannot read $filePath");
        }
        return $contents;
    }

    /**
     * Writes to the file.
     *
     * @param string $data Data to be written.
     * @return bool If the data is written or not.
     */
    protected function write(string $data)
    {
        $filePath = $this->filePath;

        if (!file_exists($filePath)) {
            throw new Exception("File doesn't exist");
        }
        if (!is_writable($filePath)) {
            throw new Exception("File is not readable");
        }

        $file = new \SplFileObject($filePath);
        $bytesWritten = $file->fwrite($data);

        if ($bytesWritten === false) {
            throw new Exception("Cannot write to $filePath");
        }
        return $bytesWritten === strlen($data);
    }

    /**
     * Saves the data to the file.
     *
     * @param integer $options The options. {@link http://php.net/json.constants}
     * @return boolean If the saving was successful or not.
     */
    public function save(int $options = JSON_PRETTY_PRINT)
    {
        return $this->write($this->getAsJson($options));
    }

    /**
     * Creates the file.
     *
     * @return true
     * @throws Exception If the file cannot be created.
     */
    protected function create()
    {
        if (!@touch($this->filePath)) {
            throw new Exception("Cannot create the file");
        }
        return true;
    }

    /**
     * Returns the filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return Path::getFilename($this->filePath);
    }

    /**
     * Returns the file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        return Path::canonicalize($this->filePath);
    }
}
