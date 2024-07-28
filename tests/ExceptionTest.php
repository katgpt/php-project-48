<?php

namespace Differ\Tests\ExceptionTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class ExceptionTest extends TestCase
{
    // Test for exception on invalid file extension;
    public function testException(): void
    {
        $path1 = __DIR__ . '/fixtures/expectedStylish.txt';
        $path2 = __DIR__ . '/fixtures/file2.json';
        $message = "Error: Invalid file extension, use json- or yaml/yml- files!\n";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);

        genDiff($path1, $path2, 'stylish');
    }

    // Check for exceptions for non-existent or unreadable files;
    public function testException2(): void
    {
        $path1 = __DIR__ . '/fixtures/file1.yaml';
        $path2 = __DIR__ . '/fixtures/file2.txt';
        $message = "Error: The file '{$path2}' do not exist or are unreadable!\n";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);

        genDiff($path1, $path2, 'stylish');
    }
}