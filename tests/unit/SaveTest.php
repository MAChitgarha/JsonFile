<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\Json;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Option\FileOpt;
use stdClass;

class SaveTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @dataProvider jsonDataProvider
     */
    public function testSavingOnTheFly($data, string $expectedContents)
    {
        // Assign
        $filePath = self::getFile(self::JSON_FILE_TEST);

        // Act
        JsonFile::saveToFile($data, $filePath, 0, 0);

        // Assert
        $this->assertEquals(
            $expectedContents,
            (new JsonFile($filePath, FileOpt::READ_ONLY))->getAsJson()
        );
    }

    /** @dataProvider dataProvider */
    public function testSave($data, string $expectedContents)
    {
        // Assign
        $filePath = self::getFile(self::JSON_FILE_TEST);
        $file = new JsonFile($filePath);

        // Act
        $file->set($data);
        $file->save(0);

        // Assert
        $this->assertEquals(
            $expectedContents,
            (new JsonFile($filePath, FileOpt::READ_ONLY))->getAsJson()
        );
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
            [[1, 2], "[1,2]"],
            [new stdClass(), "[]"],
        ];
    }

    public function jsonDataProvider()
    {
        return [
            [new Json(null), "null"],
            [new Json(false), "false"],
            [new Json(true), "true"],
            [new Json(20), "20"],
            [new Json(0.625), "0.625"],
            [new Json([]), "[]"],
            [new Json([0, 0]), "[0,0]"],
        ];
    }
}
