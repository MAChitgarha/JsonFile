<?php
/**
 * Unit tests for MAChitgarha\Component\JsonFile class.
 *
 * Go to the project's root and run the tests in this way:
 * phpunit --bootstrap vendor/autoload.php tests/unit
 * Using the --repeat option is recommended.
 *
 * @see MAChitgarha\Component\JsonFile
 */

namespace MAChitgarha\UnitTest\JsonFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JsonFile;
use MAChitgarha\Json\Exception\Exception;

/**
 * Expect \Exception in all of these tests.
 */
class ExceptionTest extends TestCase
{
    protected function setUp()
    {
        $this->expectException(Exception::class);
    }

    /**
     * Test options passed to the constructor.
     */
    public function testConstructorOptions()
    {
        new JsonFile(__DIR__ . "/data.json", JsonFile::FILE_MUST_EXIST);
        new JsonFile(__DIR__ . "/../data/bad-json.json");
    }
}
