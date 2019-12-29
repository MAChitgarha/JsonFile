<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;
use MAChitgarha\JsonFile\Option\FileOpt;
use PHPUnit\Framework\TestCase;

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
            [File::testFile, 0, 0],
            [File::emptyJsonFile, 0, 0],
        ];
    }

    public static function tearDownAfterClass(): void
    {
        foreach ([
            File::testFile,
            File::emptyJsonFile,
        ] as $file) {
            unlink($file);
        }
    }
}
