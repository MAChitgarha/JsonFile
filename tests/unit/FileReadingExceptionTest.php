<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileReadingException;
use MAChitgarha\JsonFile\Option\FileOpt;

class FileReadingExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(FileReadingException::class);
    }

    /** @dataProvider readingFileProvider */
    public function testReadingUnreadableFile(string $filePath, int $fileMode)
    {
        // Arrange
        File::create($filePath, $fileMode);

        // Act
        new JsonFile($filePath, FileOpt::READ_ONLY);

        // Assert
        // ...
    }

    /** @dataProvider readingFileProvider */
    public function testReadingUnreadableFileWithNew(string $filePath, int $fileMode)
    {
        // Arrange
        File::create($filePath, $fileMode);

        // Act
        JsonFile::new($filePath, FileOpt::READ_ONLY);

        // Assert
        // ...
    }

    /** @dataProvider readingFileProvider */
    public function testReloadingFile(string $filePath, int $fileMode)
    {
        // Arrange
        File::create($filePath, $fileMode);

        // Act
        $file = new JsonFile($filePath, FileOpt::READ_ONLY);
        chmod($filePath, $fileMode);
        $file->reload();

        // Assert
        // ...
    }

    public function readingFileProvider()
    {
        for ($i = 0000; $i < 0400; $i += 0100) {
            yield [File::testFile, $i];
        }
    }

    public static function tearDownAfterClass(): void
    {
        unlink(File::testFile);
    }
}