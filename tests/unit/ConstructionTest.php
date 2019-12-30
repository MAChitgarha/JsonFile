<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;

class ConstructionTest extends TestCase
{
    /** @dataProvider constructorArgProvider */
    public function testEqualityOfNewAndConstructor(...$args)
    {
        // Assign
        $file = new JsonFile(...$args);
        $file2 = JsonFile::new(...$args);

        // Act
        // ...

        // Assert
        $this->assertEquals($file->getFilename(), $file2->getFilename());
        $this->assertEquals($file->getFilePath(), $file2->getFilePath());
    }

    public function constructorArgProvider()
    {
        return [
            [self::getFile(self::JSON_FILE_TEST)],
            [self::getFile(self::JSON_FILE_EMPTY)],
        ];
    }
}
