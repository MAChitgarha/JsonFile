<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Exception\FileCreatingException;
use MAChitgarha\JsonFile\Option\FileOpt;
use Webmozart\PathUtil\Path;

class FileCreatingExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(FileCreatingException::class);
    }

    /** @dataProvider dirAndFileProvider */
    public function testٌWriteProtectedDirectory(string $dirPath, int $fileMode, string $fileName)
    {
        // Arrange
        if (!is_dir($dirPath)) {
            mkdir($dirPath);
        }
        chmod($dirPath, $fileMode);

        // Act
        new JsonFile(Path::join($dirPath, $fileName));

        // Assert
        // ...
    }

    public function dirAndFileProvider()
    {
        foreach (File::$testDirs as $dir) {
            for ($i = 0000; $i < 0300; $i += 0100) {
                yield [$dir, $i, "data.json"];
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (File::$testDirs as $dir) {
            chmod($dir, 0777);
            rmdir($dir);
        }
    }
}
