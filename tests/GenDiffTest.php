<?php

namespace Differ\Tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private string $pathJson1;
    private string $pathJson2;
    private string $pathYaml1;
    private string $pathYaml2;

    private string $fileExpectedStylish;
    private string $fileExpectedPlain;
    private string $fileExpectedJson;

    protected function setUp(): void
    {
        $this->pathJson1 = __DIR__ . '/fixtures/file1.json';
        $this->pathJson2 = __DIR__ . '/fixtures/file2.json';
        $this->pathYaml1 = __DIR__ . '/fixtures/file1.yaml';
        $this->pathYaml2 = __DIR__ . '/fixtures/file2.yaml';

        $this->fileExpectedStylish = __DIR__ . '/fixtures/expectedStylish.txt';
        $this->fileExpectedPlain   = __DIR__ . '/fixtures/expectedPlain.txt';
        $this->fileExpectedJson    = __DIR__ . '/fixtures/expectedJson.json';
    }

    public function testGenDiff(): void
    {
        // Difference between two json - files with default formatter
        $this->assertStringEqualsFile(
            $this->fileExpectedStylish,
            genDiff($this->pathJson1, $this->pathJson2)
        );

        // Difference between two json - files with 'plain' formatter
        $this->assertStringEqualsFile(
            $this->fileExpectedPlain,
            genDiff($this->pathJson1, $this->pathJson2, 'plain')
        );

        // Difference between two json - files with 'stylish' formatter
        $this->assertStringEqualsFile(
            $this->fileExpectedStylish,
            genDiff($this->pathJson1, $this->pathJson2, 'stylish')
        );

        // Difference between json & yaml - files with 'stylish' formatter
        $this->assertStringEqualsFile(
            $this->fileExpectedStylish,
            genDiff($this->pathJson1, $this->pathYaml2, 'stylish')
        );

        // Difference between yaml & yaml - files with 'stylish' formatter
        $this->assertStringEqualsFile(
            $this->fileExpectedStylish,
            genDiff($this->pathYaml1, $this->pathYaml2, 'stylish')
        );

        // Difference between yaml & yaml - files with 'plain' formatter
        $this->assertStringEqualsFile(
            $this->fileExpectedPlain,
            genDiff($this->pathYaml1, $this->pathYaml2, 'plain')
        );

        // Difference between two json - files with 'json-formatter'
        $this->assertStringEqualsFile(
            $this->fileExpectedJson,
            genDiff($this->pathJson1, $this->pathJson2, 'json')
        );

        // Difference between yaml & yaml - files with 'json-formatter'
        $this->assertStringEqualsFile(
            $this->fileExpectedJson,
            genDiff($this->pathYaml1, $this->pathYaml2, 'json')
        );
    }
}

