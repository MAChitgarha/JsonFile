<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\Json\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->expectException(InvalidArgumentException::class);
    }

    public function testNewWithoutArguments()
    {
        // Arrange
        // ...

        // Act
        JsonFile::new();

        // Assert
        // ...
    }
}
