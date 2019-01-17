<?php

namespace MAChitgarha\UnitTest\JSONFile;

use PHPUnit\Framework\TestCase;
use MAChitgarha\Component\JSONFile;
use Webmozart\PathUtil\Path;
use MAChitgarha\Component\JSON;

class MethodTest extends TestCase
{
    /** @var string Prefix path for JSON files to be loaded by JSONFile class. */
    protected $filesPrefix = __DIR__ . "/../data/";

    /** @var string[] A set of JSON files to work with them. */
    protected $files = [];

    protected function setUp()
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

    protected function loadJsonFile(string $filename)
    {
        return new JSONFile($this->files[$filename]["path"]);
    }

    public function testSave()
    {
        $jsonFile = $this->loadJsonFile("empty");
        $jsonFile->set("apps.recent", [
            "Terminal",
            "Settings",
            "Calculator"
        ]);

        $jsonFile->save();

        $this->assertEquals(3, $this->loadJsonFile("empty")->count("apps.recent"));

        return $jsonFile;
    }

    public function testGetFileInfo(JSONFile $jsonFile)
    {
        $this->loadJsonFile("empty");

        $this->assertContains("tests/data/empty.json", $jsonFile->getFilePath());
        $this->assertEquals("empty.json", $jsonFile->getFilename());
    }

    protected function tearDown()
    {
        foreach ($this->files as $file)
            file_put_contents($file["path"], new JSON($file["contents"]));
    }
}
