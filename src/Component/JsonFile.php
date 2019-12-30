<?php
/**
 * JsonFile class file.
 *
 * @author Mohammad Amin Chitgarha <machitgarha@outlook.com>
 * @see https://github.com/MAChitgarha/JsonFile
 * @see https://packagist.org/machitgarha/jsonfile
 */

namespace MAChitgarha\Component;

use MAChitgarha\Json\Exception\InvalidArgumentException;
use Webmozart\PathUtil\Path;
use MAChitgarha\JsonFile\Option\FileOpt;
use MAChitgarha\Json\Exception\InvalidJsonException;
use MAChitgarha\Json\Option\JsonOpt;
use MAChitgarha\JsonFile\Exception\FileExistenceException;
use MAChitgarha\JsonFile\Exception\FileReadingException;
use MAChitgarha\JsonFile\Exception\FileWritingException;
use MAChitgarha\JsonFile\Exception\FileCreatingException;
use SplFileObject;

/**
 * Handles JSON files.
 *
 * @see https://github.com/MAChitgarha/JsonFile/wiki
 */
class JsonFile extends Json
{
    /** @var string */
    protected $filePath;

    /** @var ?\SplFileObject File handler for reading and (probably) writing. */
    protected $fileHandler;

    /** @var bool {@see FileOpt::MUST_EXIST} */
    protected $fileMustExist = false;
    /** @var bool {@see FileOpt::READ_ONLY} (Settable only via constructor) */
    protected $readOnly = false;

    /** @var int {@see self::save()} */
    public static $defaultSaveOptions = JSON_PRETTY_PRINT;

    /**
     * @param string $filePath The file path to be opened. By default, the file will be created if
     * it does not exist.
     * @param int $options A combination of JsonOpt::* and FileOpt::* options. JsonOpt::AS_JSON is
     * enabled and cannot be disabled, which means the files must contain a valid JSON data or an
     * exception will be thrown.
     * @throws InvalidJsonException If the file does not contain a valid JSON data.
     * @throws FileReadingException
     */
    public function __construct(string $filePath, int $options = 0)
    {
        $this->filePath = $filePath;
        $this->readOnly = (bool)($options & FileOpt::READ_ONLY);
        $this->setOptions($options);

        self::createIfNeeded($filePath, $this->fileMustExist);

        clearstatcache();

        self::ensureReadable($filePath);
        if (!$this->readOnly) {
            self::ensureWritable($filePath);
        }

        $this->fileHandler = new SplFileObject($filePath, $this->readOnly ? "r" : "r+");

        parent::__construct(
            self::read($this->fileHandler),
            $options | JsonOpt::AS_JSON
        );
    }

    /**
     * Creates a new JsonFile instance.
     *
     * @see JsonFile::__construct()
     */
    public static function new($filePath = null, int $options = 0): self
    {
        if ($filePath === null) {
            throw new InvalidArgumentException("File path must be set");
        }
        return new self($filePath, $options);
    }

    /**
     * Saves JSON data into a file on-the-fly.
     *
     * @param mixed $data The data to be saved.
     * @param string $filePath The file path to be opened. By default, the file will be created if
     * it does not exist.
     * @param int $fileOptions A combination of FileOpt::* constants.
     * @param int $jsonOptions A combination of JsonOpt::* constants.
     * @return void
     */
    public static function saveToFile($data, string $filePath, int $options = 0)
    {
        $jsonFile = new self($filePath, $options);
        if ($data instanceof Json) {
            $data = $data->get();
        }
        $jsonFile->set($data);
        $jsonFile->save();
    }

    /**
     * Resets all options.
     *
     * @param int $options A combination of JsonOpt::* and FileOpt::* options.
     * @return self
     */
    public function setOptions(int $options = 0): self
    {
        parent::setOptions($options);
        $this->fileMustExist = (bool)($options & FileOpt::MUST_EXIST);
        return $this;
    }

    /**
     * Returns true if the file is opened read-only.
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->isOptionSet(FileOpt::READ_ONLY);
    }

    /**
     * Creates a file if it does not exist, regarding to FileOpt::MUST_EXIST option.
     *
     * @return void
     * @throws FileExistenceException
     * @throws FileCreatingException
     */
    protected static function createIfNeeded(string $filePath, bool $fileMustExist)
    {
        if (!file_exists($filePath)) {
            if ($fileMustExist) {
                throw new FileExistenceException("File '$filePath' does not exist");
            } else {
                if (!@touch($filePath)) {
                    throw new FileCreatingException("File '$filePath' cannot be created");
                }
            }
        }
    }

    /**
     * Throws an exception if a file is not readable.
     *
     * @param string $filePath
     * @return void
     * @throws FileReadingException
     */
    protected static function ensureReadable(string $filePath)
    {
        if (!is_readable($filePath)) {
            throw new FileReadingException("File '$filePath' is not readable");
        }
    }

    /**
     * Throws an exception if a file is not writable.
     *
     * @param string $filePath
     * @return void
     * @throws FileWritingException
     */
    protected static function ensureWritable(string $filePath)
    {
        if (!is_writable($filePath)) {
            throw new FileWritingException("File '$filePath' is not writable");
        }
    }

    /**
     * Returns the contents of a file.
     *
     * @param SplFileObject $fileHandler
     * @return string Returns "null" if the file is empty, otherwise the file contents are returned.
     * @throws FileReadingException
     */
    protected static function read(SplFileObject $fileHandler): string
    {
        self::ensureReadable($filePath = $fileHandler->getPathname());

        $fileSize = $fileHandler->getSize();
        if ($fileSize === 0) {
            // "null" is a the JSON value of null value
            return "null";
        }

        $data = $fileHandler->fread($fileSize);
        if ($data === false) {
            throw new FileReadingException("Cannot read from file '$filePath'");
        }
        return $data;
    }

    /**
     * Writes data to the specified file.
     *
     * @param SplFileObject $fileHandler
     * @param string $data Data to be written.
     * @param bool $readOnly Whether or not the file is read-only.
     * @return void
     * @throws FileWritingException
     */
    protected static function write(SplFileObject $fileHandler, string $data, bool $readOnly)
    {
        if ($readOnly) {
            throw new FileWritingException("File is read-only");
        }

        self::ensureWritable($filePath = $fileHandler->getPathname());

        // Making the file empty
        $fileHandler->ftruncate(0);
        $fileHandler->rewind();

        $writtenBytes = $fileHandler->fwrite($data);
        if (!$writtenBytes || $writtenBytes !== strlen($data)) {
            throw new FileWritingException("Cannot write to file '$filePath'");
        }
    }

    /**
     * Saves current data to the file.
     *
     * @param int $options The options. {@link http://php.net/json.constants} Default save options
     * is handled by self::$defaultSaveOptions static property.
     * @return self
     * @throws FileWritingException
     */
    public function save(int $options = null): self
    {
        if ($options === null) {
            $options = self::$defaultSaveOptions;
        }

        self::createIfNeeded($this->filePath, $this->fileMustExist);
        self::write($this->fileHandler, $this->getAsJson($options), $this->readOnly);

        return $this;
    }

    /**
     * Rereads the contents from the file.
     *
     * @return self
     * @throws FileReadingException
     */
    public function reload(): self
    {
        $this->set(self::read($this->fileHandler));
        return $this;
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
