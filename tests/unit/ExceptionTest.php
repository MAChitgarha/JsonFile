<?php
/** 
 * Unit tests for MAChitgarha\Component\JSONFile class.
 *
 * Go to the project's root and run the tests in this way:
 * phpunit --bootstrap vendor/autoload.php tests/unit
 * Using the --repeat option is recommended.
 * 
 * @see MAChitgarha\Component\JSONFile
 */

namespace MAChitgarha\UnitTest\JSONFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JSONFile;

/**
 * Expect \Exception in all of these tests.
 */
class ExceptionTest extends TestCase
{
    protected function setUp()
    {
        $this->expectException(\Exception::class);
    }

    /**
     * Test options passed to the constructor.
     */
    public function testConstructorOptions()
    {
        new JSONFile(__DIR__ . "/data.json", JSONFile::FILE_MUST_EXIST);
        new JSONFile(__DIR__ . "/../data/bad-json.json");
    }
}
