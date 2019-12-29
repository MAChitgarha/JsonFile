<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileWritingException;

class FileWritingExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(FileWritingException::class);
    }

    /** @dataProvider writingFileProvider */
    public function testWritingUnwritableFile(string $filePath, int $fileMode)
    {
        // Arrange
        File::create($filePath, $fileMode);

        // Act
        new JsonFile($filePath);

        // Assert
        // ...
    }

    /** @dataProvider writingFileProvider */
    public function testWritingAfterMakingUnwritable(string $filePath, int $fileMode)
    {
        // Arrange
        File::create($filePath, 0777);

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
            yield [File::testFile, $i];
        }
    }

    public static function tearDownAfterClass(): void
    {
        unlink(File::testFile);
    }
}
