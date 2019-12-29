<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\Json\Exception\Exception;

class ExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(Exception::class);
    }

    public function testInvalidJsonFile()
    {
        // Assign
        // ...

        // Act
        new JsonFile(File::invalidJsonFile);

        // Assert
        // ...
    }
}
