<?php

/** @see MAChitgarha\Component\JsonFile */
namespace MAChitgarha\UnitTest\JsonFile;

use MAChitgarha\Component\JsonFile;
use MAChitgarha\Json\Option\JsonOpt;
use MAChitgarha\JsonFile\Option\FileOpt;

class OptionsTest extends TestCase
{
    /** @dataProvider setOptionsProvider */
    public function testSettingOptions(int $options, array $optionsArr)
    {
        // Assign
        $file = new JsonFile(self::getFile(self::JSON_FILE_TEST));

        // Act
        $file->setOptions($options);

        // Assert
        foreach ($optionsArr as $option) {
            $this->assertTrue($file->isOptionSet($option));
        }
    }

    /** @dataProvider constructorOptionsProvider */
    public function testConstructorOptions(int $options, array $optionsArr)
    {
        // Assign
        $file = new JsonFile(self::getFile(self::JSON_FILE_TEST), $options);

        // Act
        // ...

        // Assert
        foreach ($optionsArr as $option) {
            $this->assertTrue($file->isOptionSet($option));
        }
    }

    /** @dataProvider singleOptionProvider */
    public function testAddingAndRemovingOptions(int $option)
    {
        // Assign
        $file = new JsonFile(self::getFile(self::JSON_FILE_TEST));

        // Act
        $file->addOption($option);

        // Assert
        $this->assertTrue($file->isOptionSet($option));

        // Act
        $file->removeOption($option);

        // Assert
        $this->assertFalse($file->isOptionSet($option));
    }

    public function setOptionsProvider()
    {
        $options = [
            [FileOpt::MUST_EXIST, JsonOpt::DECODE_ALWAYS],
        ];
        yield from $this->generateOptions($options);
    }

    public function constructorOptionsProvider()
    {
        $options = [
            [FileOpt::MUST_EXIST],
            [FileOpt::READ_ONLY],
            [FileOpt::MUST_EXIST, FileOpt::READ_ONLY, JsonOpt::DECODE_ALWAYS],
        ];
        yield from $this->generateOptions($options);
    }

    public function generateOptions(array $optionsArrSet)
    {
        foreach ($optionsArrSet as $optionsArr) {
            $options = 0;
            foreach ($optionsArr as $option) {
                $options |= $option;
            }
            yield [$options, $optionsArr];
        }
    }

    public function singleOptionProvider()
    {
        return [
            [FileOpt::MUST_EXIST],
            [JsonOpt::DECODE_ALWAYS],
        ];
    }
}
