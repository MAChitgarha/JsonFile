<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

class File
{
    private const dataPath = __DIR__ . "/../data";
    public const testFile = self::dataPath . "/test.json";
    public const testDir = self::dataPath . "/test";
    public const emptyJsonFile = self::dataPath . "/empty.json";
    public const invalidJsonFile = self::dataPath . "/invalid.json";

    public static function create(string $filePath, int $fileMode)
    {
        touch($filePath);
        chmod($filePath, $fileMode);
    }
}
