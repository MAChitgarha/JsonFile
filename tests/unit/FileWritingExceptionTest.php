<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileWritingException;

class FileWritingExceptionTest extends TestCase
{
    public static $files = [
        __DIR__ . "/data.json",
    ];

    private static function create(string $filePath, int $fileMode)
    {
        touch($filePath);
        chmod($filePath, $fileMode);
    }

    protected function setUp(): void
    {
        $this->expectException(FileWritingException::class);
    }

    /** @dataProvider writingFileProvider */
    public function testWritingUnwritableFile(string $filePath, int $fileMode)
    {
        // Arrange
        self::create($filePath, $fileMode);

        // Act
        new JsonFile($filePath);

        // Assert
        // ...
    }

    /** @dataProvider writingFileProvider */
    public function testWritingAfterMakingUnwritable(string $filePath, int $fileMode)
    {
        // Arrange
        self::create($filePath, 0777);

        // Act
        $file = new JsonFile($filePath);
        $file->set([]);

        chmod($filePath, $fileMode);
        $file->save();

        // Assert
        // ...
    }

    public function writingFileProvider()
    {
        foreach (self::$files as $file) {
            for ($i = 0400; $i < 0600; $i += 0100) {
                yield [$file, $i];
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::$files as $file) {
            unlink($file);
        }
    }
}
