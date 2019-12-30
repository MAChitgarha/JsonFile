<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\Json;
use MAChitgarha\Component\JsonFile;
use stdClass;

class SaveTest extends TestCase
{
    /** @dataProvider dataProvider */
    public function testSavingOnTheFly($data, string $expectedContents)
    {
        // Assign
        $filePath = self::getFile(self::JSON_FILE_TEST);

        // Act
        JsonFile::saveToFile($data, $filePath, 0, 0);

        // Assert
        $this->assertEquals($expectedContents, file_get_contents($filePath));
    }

    public function dataProvider()
    {
        return [
            [null, "null"],
            [false, "false"],
            [true, "true"],
            [19, "19"],
            [3.14, "3.14"],
            [[], "[]"],
            [[1], "[1]"],
            [new stdClass(), "[]"],
            [new Json([]), "[]"]
        ];
    }
}
