<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use InvalidArgumentException;
use MAChitgarha\Component\Pusheh;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use RuntimeException;

abstract class TestCase extends PHPUnitTestCase
{
    // Directories
    public const DATA_PATH = __DIR__ . "/../data";
    public const TEST_DIR = self::DATA_PATH . "/test";

    public const JSON_FILE_TEST = "test";
    public const JSON_FILE_EMPTY = "empty";
    public const JSON_FILE_INVALID = "invalid";

    /**
     * A set of files to work with. Each file has two fields, a name, and whether it's anonymous or
     * not. An anonymous file means that it doesn't exist before class, and must be removed when a
     * test is ended.
     *
     * @var array
     */
    private static $files = [
        self::JSON_FILE_TEST => [
            "name" => self::DATA_PATH . "/test.json",
            "anonymous" => true,
        ],
        self::JSON_FILE_EMPTY => [
            "name" => self::DATA_PATH . "/empty.json",
            "anonymous" => false,
        ],
        self::JSON_FILE_INVALID => [
            "name" => self::DATA_PATH . "/invalid.json",
            "anonymous" => false,
        ],
    ];

    /**
     * Saves the contents of non-anonymous files to revert back the contents after tests.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        foreach (self::$files as &$file) {
            if (!$file["anonymous"]) {
                if (!file_exists($file["name"])) {
                    throw new RuntimeException("File '{$file['name']}' must be existed");
                }
                $file["contents"] = file_get_contents($file["name"]);
            }
        }
    }

    public static function getFile(string $which)
    {
        if (isset(self::$files[$which]["name"])) {
            return self::$files[$which]["name"];
        }
        throw new InvalidArgumentException("No such file exist ('$which')");
    }

    public static function createFile(string $which, int $fileMode): string
    {
        $filePath = self::getFile($which);
        touch($filePath);
        chmod($filePath, $fileMode);
        return $filePath;
    }

    /**
     * Get files and directories to the default state.
     *
     * Removes anonymous files, and revert the contents of permanent files back.
     * Also, remove directories that was created previously.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // Files
        foreach (self::$files as $file) {
            $filename = $file["name"];
            if ($file["anonymous"]) {
                if (file_exists($filename)) {
                    unlink($filename);
                }
            } else {
                file_put_contents($filename, $file["contents"]);
            }
        }

        // Directories
        if (is_dir(self::TEST_DIR)) {
            chmod(self::TEST_DIR, 0777);
            Pusheh::removeDirRecursive(self::TEST_DIR);
        }
    }
}
