<?php
/**
 * JSONFile class file.
 *
 * @author Mohammad Amin Chitgarha <machitgarha@outlook.com>
 * @see https://github.com/MAChitgarha/JSONFile
 * @todo Add link to the packagist.
 */

namespace MAChitgarha\Component;

use Webmozart\PathUtil\Path;

/**
 * Handles JSON files.
 *
 * Reads a JSON file, make some operations on it and saves it.
 *
 * @todo Import the link of the GitHub Wiki.
 */
class JSONFile extends JSON
{
    /** @var string */
    protected $filePath;

    /** @var bool {@see JSONFile::FILE_MUST_EXIST} */
    protected $fileMustExist = false;

    /** @var bool {@see JSONFile::OVERWRITE_INVALID_FILE} */
    protected $overwriteInvalidFile = false;

    /** @var int If it is set, the file must exist, otherwise it will throw an exception. */
    const FILE_MUST_EXIST = 1;
    /** @var int If it is set, the file will be overwritten with an empty array when it contains invalid JSON data (e.g. {""}). */
    const OVERWRITE_INVALID_FILE = 2;

    /**
     * Reads the JSON file data.
     *
     * The default behavior (to override these settings, use options argument):
     * If the file doesn't exist, it will create.
     * If the file contains invalid JSON data, then it will throw an exception.
     *
     * @param string $filePath File path to be read.
     * @param integer $options Available options: FILE_MUST_EXIST, OVERWRITE_INVALID_FILE
     * @throws \Exception When the file doesn't exist and FILE_MUST_EXIST is on.
     * @throws \Exception When the file contains invalid JSON and OVERWRITE_INVALID_FILE is off.
     */
    public function __construct(string $filePath, int $options = 0)
    {
        // Extract options
        $this->fileMustExist = (bool)($options & self::FILE_MUST_EXIST);
        $this->overwriteInvalidFile = (bool)($options & self::OVERWRITE_INVALID_FILE);

        $this->filePath = $filePath;

        $data = "";
        // Read the file
        try {
            $data = $this->read();
            // The file doesn't exist
        } catch (\Exception $e) {
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
            parent::__construct($data);
            // The file doesn't contain an invalid JSON
        } catch (\InvalidArgumentException $e) {
            if (!$this->overwriteInvalidFile) {
                throw new \Exception("File does not contain a valid JSON");
            }
            parent::__construct();
        }
    }

    /**
     * Reads from the file.
     *
     * @return string File contents.
     * @throws \Exception When the file doesn't exist.
     * @throws \Exception When the file isn't readable (e.g. permission denied).
     */
    protected function read()
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("File doesn't exist");
        }
        if (!is_readable($this->filePath)) {
            throw new \Exception("File is not readable");
        }

        return file_get_contents($this->filePath);
    }

    /**
     * Writes to the file.
     *
     * @param string $data Data to be written.
     * @return boolean If the data is written or not.
     */
    protected function write(string $data)
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("File doesn't exist");
        }
        if (!is_writable($this->filePath)) {
            throw new \Exception("File is not readable");
        }

        // Write to the file
        $bytesWritten = @file_put_contents($this->filePath, $data);

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
        return $this->write($this->getDataAsJson($options));
    }

    /**
     * Creates the file.
     *
     * @return true
     * @throws \Exception If the file cannot be created.
     */
    protected function create()
    {
        if (!@touch($this->filePath)) {
            throw new \Exception("Cannot create the file");
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
