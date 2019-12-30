<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;

class ReloadTest extends TestCase
{
    /** @dataProvider dataProvider */
    public function testReloading($data, $newData)
    {
        // Assign
        $filePath = self::getFile(self::JSON_FILE_TEST);
        $file = new JsonFile($filePath);
        $file2 = new JsonFile($filePath);

        // Act
        $file->set($data);
        $file->save();
        $file2->set($newData);
        $file2->save();
        $file->reload();

        // Assert
        $this->assertEquals($newData, $file2->get());
        $this->assertEquals($newData, $file->get());
    }

    public function dataProvider()
    {
        return [
            [null, [1, 2]],
            [false, []],
            [true, 0],
            [19, "24"],
        ];
    }
}
