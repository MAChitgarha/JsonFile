<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileCreatingException;
use Webmozart\PathUtil\Path;

class FileCreatingExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(FileCreatingException::class);
    }

    /** @dataProvider dirAndFileProvider */
    public function testWriteProtectedDirectory(string $dirPath, int $dirMode, string $fileName)
    {
        // Arrange
        if (!is_dir($dirPath)) {
            mkdir($dirPath);
        }
        chmod($dirPath, $dirMode);

        // Act
        new JsonFile(Path::join($dirPath, $fileName));

        // Assert
        // ...
    }

    public function testEmptyFilePath()
    {
        // Arrange
        // ...

        // Act
        new JsonFile("");

        // Assert
        // ...
    }

    public function dirAndFileProvider()
    {
        for ($i = 0000; $i < 0300; $i += 0100) {
            yield [self::TEST_DIR, $i, "test.json"];
        }
    }
}
