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

    /** @var int A combination of FileOpt::* options. */
    protected $fileOptions;

    /** @var bool {@see FileOpt::MUST_EXIST} */
    protected $fileMustExist = false;
    /** @var bool {@see FileOpt::READ_ONLY} (Settable only via constructor) */
    protected $readOnly = false;

    /** @var int {@see self::save()} */
    public static $defaultSaveOptions = JSON_PRETTY_PRINT;

    /**
     * @param string $filePath The file path to be opened. By default, the file will be created if
     * it does not exist.
     * @param int $fileOptions A combination of FileOpt::* constants.
     * @param int $jsonOptions A combination of JsonOpt::* constants.
     * @throws InvalidJsonException If the file does not contain a valid JSON data.
     * @throws FileReadingException
     */
    public function __construct(string $filePath, int $fileOptions = 0, int $jsonOptions = 0)
    {
        $this->setOptions($fileOptions);
        $this->filePath = $filePath;

        self::createIfNeeded($filePath, $this->fileMustExist);

        if (!is_readable($filePath)) {
            throw new FileReadingException("File '$filePath' is not readable");
        }

        clearstatcache();

        $this->readOnly = (bool)($fileOptions & FileOpt::READ_ONLY);
        $this->fileHandler = new SplFileObject($filePath, $this->readOnly ? "r" : "r+");

        parent::__construct(
            self::read($this->fileHandler),
            $jsonOptions
        );
    }

    /**
     * Creates a new JsonFile instance.
     *
     * @param string $filePath The file path to be opened. By default, the file will be created if
     * it does not exist.
     * @param int $fileOptions A combination of FileOpt::* constants.
     * @param int $jsonOptions A combination of JsonOpt::* constants.
     * @throws InvalidJsonException If the file does not contain a valid JSON data.
     * @throws FileReadingException
     * @return self
     */
    public static function new($filePath = "", int $fileOptions = 0, int $jsonOptions = 0): self
    {
        if ($filePath === "") {
            throw new InvalidArgumentException("File path cannot be empty");
        }

        return new self($filePath, $fileOptions, $jsonOptions);
    }

    /**
     * Resets all options.
     *
     * @param int $options A combination of JsonOpt::* options or FileOpt::* ones.
     * @param string $optionType Specifies what the options belong to. Should be a class name.
     * @return self
     */
    public function setOptions(int $options = 0, string $optionType = self::class): self
    {
        if ($optionType === self::class) {
            $this->fileOptions = $options;
            $this->fileMustExist = (bool)($options & FileOpt::MUST_EXIST);
        } else {
            parent::setOptions($options);
        }
        return $this;
    }

    /**
     * Sets an option.
     *
     * @param int $options A combination of JsonOpt::* options or FileOpt::* ones.
     * @param string $optionType Specifies what the options belong to. Should be a class name.
     * @return self
     */
    public function addOption(int $option, string $optionType = self::class): self
    {
        if ($optionType === self::class) {
            $this->setOptions($this->fileOptions | $option);
        } else {
            parent::addOption($option);
        }
        return $this;
    }

    /**
     * Unsets an option.
     *
     * @param int $options A combination of JsonOpt::* options or FileOpt::* ones.
     * @param string $optionType Specifies what the options belong to. Should be a class name.
     * @return self
     */
    public function removeOption(int $option, string $optionType = self::class)
    {
        if ($optionType === self::class) {
            $this->setOptions($this->fileOptions & ~$option);
        } else {
            parent::removeOption($option);
        }
        return $this;
    }

    /**
     * Tells whether an option is set or not.
     *
     * @param int $options A combination of JsonOpt::* options or FileOpt::* ones.
     * @param string $optionType Specifies what the options belong to. Should be a class name.
     * @return bool
     */
    public function isOptionSet(int $option, string $optionType = self::class): bool
    {
        if ($optionType === self::class) {
            return ($this->fileOptions & $option) === $option;
        } else {
            return parent::isOptionSet($option);
        }
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
     * Returns the contents of a file.
     *
     * @param SplFileObject $fileHandler
     * @return null|string Returns null if the file is empty.
     */
    protected static function read(SplFileObject $fileHandler)
    {
        $fileSize = $fileHandler->getSize();
        if ($fileSize === 0) {
            $data = null;
        } else {
            $data = $fileHandler->fread($fileSize);
            if ($data === false) {
                $filePath = $fileHandler->getPathname();
                throw new FileReadingException("Cannot read from file '$filePath'");
            }
        }
        return $data;
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
        if ($this->readOnly) {
            throw new FileWritingException("File is opened as read-only");
        }

        if ($options === null) {
            $options = self::$defaultSaveOptions;
        }

        self::createIfNeeded($this->filePath, $this->fileMustExist);
        $filePath = $this->filePath;

        if (!is_writable($filePath)) {
            throw new FileWritingException("File '$filePath' is not writable");
        }

        // Making the file empty
        $this->fileHandler->ftruncate(0);
        $this->fileHandler->rewind();

        $writtenBytes = $this->fileHandler->fwrite($dataString = $this->getAsJson($options));
        if ($writtenBytes === null || $writtenBytes !== strlen($dataString)) {
            throw new FileWritingException("Cannot write to file '$filePath'");
        }

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
    public static function saveToFile($data, string $filePath, int $fileOptions = 0,
        int $jsonOptions = 0)
    {
        $jsonFile = new self($filePath, $fileOptions, $jsonOptions);
        $jsonFile->set($data);
        $jsonFile->save();
    }
}
