<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileWritingException;

class FileWritingExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(FileWritingException::class);
    }

    /** @dataProvider writingFileProvider */
    public function testWritingUnwritableFile(string $fileId, int $fileMode)
    {
        // Arrange
        $filePath = self::createFile($fileId, $fileMode);

        // Act
        new JsonFile($filePath);

        // Assert
        // ...
    }

    /** @dataProvider writingFileProvider */
    public function testWritingAfterMakingUnwritable(string $fileId, int $fileMode)
    {
        // Arrange
        $filePath = self::createFile($fileId, 0777);

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
        for ($i = 0400; $i < 0600; $i += 0100) {
            yield [self::JSON_FILE_TEST, $i];
        }
    }
}
