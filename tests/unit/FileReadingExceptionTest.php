<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

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
    public function testReadingUnreadableFile(string $fileId, int $fileMode)
    {
        // Arrange
        self::createFile($fileId, $fileMode);

        // Act
        new JsonFile(self::getFile($fileId), FileOpt::READ_ONLY);

        // Assert
        // ...
    }

    /** @dataProvider readingFileProvider */
    public function testReadingUnreadableFileWithNew(string $fileId, int $fileMode)
    {
        // Arrange
        self::createFile($fileId, $fileMode);

        // Act
        JsonFile::new(self::getFile($fileId), FileOpt::READ_ONLY);

        // Assert
        // ...
    }

    /** @dataProvider readingFileProvider */
    public function testReloadingFile(string $fileId, int $fileMode)
    {
        // Arrange
        $filePath = self::getFile($fileId);
        self::createFile($fileId, $fileMode);

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
            yield [self::JSON_FILE_TEST, $i];
        }
    }
}
