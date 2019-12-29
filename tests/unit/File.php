<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

class File
{
    public static $testFiles = [
        __DIR__ . "/data.json",
    ];

    public static function create(string $filePath, int $fileMode)
    {
        touch($filePath);
        chmod($filePath, $fileMode);
    }
}
