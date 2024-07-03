<?php

namespace PhpProject48\tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function PhpProject48\Src\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $file1 = file_get_contents(__DIR__ . '/fixtures/file1.json');
        $file2 = file_get_contents(__DIR__ . '/fixtures/file2.json');
        $actual = genDiff($file1, $file2, 'stylish');
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedStylish');
        $this->assertEquals($expected, $actual);

        $file1 = file_get_contents(__DIR__ . '/fixtures/file1.yaml');
        $file2 = file_get_contents(__DIR__ . '/fixtures/file2.yaml');
        $actual = genDiff($file1, $file2, 'stylish');
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedStylish');
        $this->assertEquals($expected, $actual);

        $file1 = file_get_contents(__DIR__ . '/fixtures/file1.json');
        $file2 = file_get_contents(__DIR__ . '/fixtures/file2.json');
        $actual = genDiff($file1, $file2, 'json');
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedJson');
        $this->assertEquals($expected, $actual);

        $file1 = file_get_contents(__DIR__ . '/fixtures/file1.yaml');
        $file2 = file_get_contents(__DIR__ . '/fixtures/file2.yaml');
        $actual = genDiff($file1, $file2, 'json');
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedJson');
        $this->assertEquals($expected, $actual);

        /*$file1 = file_get_contents(__DIR__ . '/fixtures/file1.json');
        $file2 = file_get_contents(__DIR__ . '/fixtures/file2.json');
        $actual = genDiff($file1, $file2, 'plain');
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedPlain');
        $this->assertEquals($expected, $actual);

        $file1 = file_get_contents(__DIR__ . '/fixtures/file1.yaml');
        $file2 = file_get_contents(__DIR__ . '/fixtures/file2.yaml');
        $actual = genDiff($file1, $file2, 'plain');
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedPlain');
        $this->assertEquals($expected, $actual);*/
    }
}
