<?php

namespace MAChitgarha\UnitTest\JSONFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JSONFile;

class ExceptionTest extends TestCase
{
    protected function setUp()
    {
        $this->expectException(\Exception::class);
    }

    public function testConstants()
    {
        new JSONFile(__DIR__ . "/data.json", JSONFile::FILE_MUST_EXIST);
        new JSONFile(__DIR__ . "/../data/bad-json.json");
    }
}
