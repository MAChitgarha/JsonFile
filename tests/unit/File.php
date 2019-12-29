<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

class File
{
    private const dataPath = __DIR__ . "/../data";
    public const testFile = self::dataPath . "/test.json";
    public const testDir = self::dataPath . "/test";

    public static function create(string $filePath, int $fileMode)
    {
        touch($filePath);
        chmod($filePath, $fileMode);
    }
}
