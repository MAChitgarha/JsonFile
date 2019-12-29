<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileReadingException;

class FileReadingExceptionTest extends TestCase
{
    private static $testFilePath = __DIR__ . "/data.json";

    public function testReadingUnreadableFile()
    {
        $this->expectException(FileReadingException::class);

        // Arrange
        touch(self::$testFilePath);
        chmod(self::$testFilePath, 0000);

        // Act
        new JsonFile(self::$testFilePath);

        // Assert
        // ...
    }

    protected function tearDown(): void
    {
        unlink(self::$testFilePath);
    }
}
