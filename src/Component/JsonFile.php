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
use MAChitgarha\JsonFile\Option\FileOpt;
use MAChitgarha\Json\Exception\Exception;
use MAChitgarha\Json\Exception\InvalidJsonException;
use MAChitgarha\JsonFile\Exception\FileExistenceException;
use MAChitgarha\JsonFile\Exception\FileReadingException;
use MAChitgarha\JsonFile\Exception\FileWritingException;
use MAChitgarha\JsonFile\Exception\FileCreatingException;

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

    /** @var \SplFileObject File handler for reading and writing. */
    protected $fileHandler;
    
    /** @var bool {@see FileOpt::MUST_EXIST} */
    protected $fileMustExist = false;

    /**
     * Reads the JSON file data.
     *
     * The file will be created if it does not exist.
     *
     * @param string $filePath
     * @param int $fileOptions A combination of FileOpt::* constants.
     * @param int $jsonOptions A combination of JsonOpt::* constants.
     * @throws InvalidJsonException If the file does not contain a valid JSON data.
     */
    public function __construct(string $filePath, int $fileOptions = 0, int $jsonOptions = 0)
    {
        // Setting options
        $this->fileMustExist = (bool)($fileOptions & FileOpt::MUST_EXIST);

        $this->filePath = $filePath;

        $this->createFileIfNotExists();

        if (!is_readable($filePath)) {
            throw new FileReadingException("File '$filePath' is not readable.");
        }
        
        $fileHandler = new \SplFileObject($filePath, "r+");
        $this->fileHandler = $fileHandler;

        $data = $fileHandler->fread($fileHandler->getSize());
        if ($data === false) {
            throw new FileReadingException("Cannot read from file '$filePath'");
        }
        if ($data === "") {
            $data = null;
        }

        parent::__construct($data, $jsonOptions);
    }

    /**
     * Creates a file if it does not exist, with handling FileOpt::MUST_EXIST option. 
     *
     * @return void
     * @throws FileExistenceException
     * @throws FileCreatingException
     */
    protected function createFileIfNotExists()
    {
        $filePath = $this->filePath;

        if (!file_exists($filePath)) {
            if ($this->fileMustExist) {
                throw new FileExistenceException("File '$filePath' does not exist");
            } else {
                if (!@touch($filePath)) {
                    throw new FileCreatingException("File '$filePath' cannot be created");
                }
            }
        }
    }

    /**
     * Saves the data to the file.
     *
     * @param integer $options The options. {@link http://php.net/json.constants}
     * @return boolean If the saving was successful or not.
     */
    public function save(int $options = JSON_PRETTY_PRINT)
    {
        $this->createFileIfNotExists();
        $filePath = $this->filePath;
        
        if (!is_writable($filePath)) {
            throw new FileWritingException("File '$filePath' is not writable");
        }

        $dataString = $this->getAsJson($options);
        $writtenBytes = $this->fileHandler->fwrite($dataString);
        if ($writtenBytes === false || strlen($writtenBytes) !== strlen($dataString)) {
            throw new FileWritingException("Cannot write to the file '$filePath'");
        }
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

    public function __destruct()
    {
        $this->fileHandler = null;
    }
}
