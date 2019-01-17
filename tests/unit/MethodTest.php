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
        $appsJson = $this->loadJsonFile("empty");
        $appsJson->set("apps.recent", [
            "Terminal",
            "Settings",
            "Calculator"
        ]);

        $appsJson->save();

        $this->assertEquals(3, $this->loadJsonFile("empty")->count("apps.recent"));
    }

    protected function tearDown()
    {
        foreach ($this->files as $file)
            file_put_contents($file["path"], new JSON($file["contents"]));
    }
}
