<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileExistenceException;
use MAChitgarha\JsonFile\Option\FileOpt;

class FileExistenceExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(FileExistenceException::class);
    }

    /** @dataProvider fileProvider */
    public function testNonExistedFile(string $filePath)
    {
        // Arrange
        // ...

        // Act
        new JsonFile($filePath, FileOpt::MUST_EXIST);

        // Assert
        // ...
    }

    public function fileProvider()
    {
        return [
            [File::testFile]
        ];
    }
}
