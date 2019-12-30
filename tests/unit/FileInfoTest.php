<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;
use Webmozart\PathUtil\Path;

class FileInfoTest extends TestCase
{
    protected function setUp(): void
    {
        mkdir(self::TEST_DIR);
    }

    /** @dataProvider fileProvider */
    public function testGettingFileInfo(string $filename)
    {
        // Assign
        $file = new JsonFile($filename);
        $file2 = new JsonFile($file->getFilePath());

        // Act
        // ...

        // Assert
        $this->assertEquals(Path::getFilename($filename), $file->getFilename());
        $this->assertEquals($file->getFilename(), $file2->getFilename());
        $this->assertEquals($file->getFilePath(), $file2->getFilePath());
    }

    public function fileProvider()
    {
        return [
            [self::getFile(self::JSON_FILE_TEST)],
            [self::getFile(self::JSON_FILE_EMPTY)],
            [Path::join(self::TEST_DIR, "test.json")],
        ];
    }
}
