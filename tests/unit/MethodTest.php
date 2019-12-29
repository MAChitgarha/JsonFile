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
use Webmozart\PathUtil\Path;
use MAChitgarha\Component\JSON;

/**
 * Tests all methods individually.
 */
class MethodTest extends TestCase
{
    /** @var string Prefix path for JSON files to be loaded by JsonFile class. */
    protected $filesPrefix = __DIR__ . "/../data/";

    /** @var array[] A set of JSON files to work with them. {@see self::setUp()} */
    protected $files = [];

    /**
     * Prepare $this->files.
     *
     * Set 'files' property to contain an array consisting JSON files' data to be used in tests.
     * The index is the file's name without its extension, e.g., if the filename is 'empty.json',
     * then the index of its data would be 'empty'.
     * Each array element has two indexes:
     * path: specifies the path of the JSON file,
     * contents: the default file contents. {@see self::tearDown()}
     */
    protected function setUp(): void
    {
        $fileData = [
            [
                "empty",
                []
            ]
        ];

        foreach ($fileData as $fileDatum) {
            $name = $fileDatum[0];
            $fileContents = $fileDatum[1];

            $this->files[$name] = [
                "path" => Path::join($this->filesPrefix, "$name.json"),
                "contents" => $fileContents
            ];
        }
    }

    /**
     * Returns a JsonFile instance using one of the files in 'files' property.
     */
    protected function loadJsonFile(string $filename)
    {
        return new JsonFile($this->files[$filename]["path"]);
    }

    /**
     * Tests JsonFile::save() method.
     */
    public function testSave()
    {
        $jsonFile = $this->loadJsonFile("empty");
        $jsonFile->set([
            "Terminal",
            "Settings",
            "Calculator"
        ], "apps.recent");

        $jsonFile->save();

        // Check if the file saved correctly or not
        $this->assertEquals(3, $this->loadJsonFile("empty")->count("apps.recent"));
    }

    /**
     * Tests JsonFile::getFilename() and JsonFile::getFilePath() methods.
     */
    public function testGetFileInfo()
    {
        $jsonFile = $this->loadJsonFile("empty");

        $this->assertStringContainsString("tests/data/empty.json", $jsonFile->getFilePath());
        $this->assertEquals("empty.json", $jsonFile->getFilename());
    }

    /**
     * Resets all changed files.
     *
     * Resets all JSON files that has been changed during tests to their default contents for future
     * tests.
     */
    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            file_put_contents($file["path"], new JSON($file["contents"]));
        }
    }
}
