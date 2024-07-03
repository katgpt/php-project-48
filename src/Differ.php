<?php

namespace PhpProject48\Src\Differ;

use function PhpProject48\Src\Parsers\parse;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish'): string
{
    $parsedContentOfFile1 = parse($pathToFile1);
    $parsedContentOfFile2 = parse($pathToFile2);

    $keys = array_unique(array_merge(array_keys($parsedContentOfFile1), array_keys($parsedContentOfFile2)));
    sort($keys);

    $diff = [];
    foreach ($keys as $key) {
        if (!array_key_exists($key, $parsedContentOfFile1)) {
            $value = is_bool($parsedContentOfFile2[$key]) ? var_export($parsedContentOfFile2[$key], true) : $parsedContentOfFile2[$key];
            $diff[] = "+ {$key}: {$value}";
        } elseif (!array_key_exists($key, $parsedContentOfFile2)) {
            $value = is_bool($parsedContentOfFile1[$key]) ? var_export($parsedContentOfFile1[$key], true) : $parsedContentOfFile1[$key];
            $diff[] = "- {$key}: {$value}";
        } elseif ($parsedContentOfFile1[$key] !== $parsedContentOfFile2[$key]) {
            $value1 = is_bool($parsedContentOfFile1[$key]) ? var_export($parsedContentOfFile1[$key], true) : $parsedContentOfFile1[$key];
            $value2 = is_bool($parsedContentOfFile2[$key]) ? var_export($parsedContentOfFile2[$key], true) : $parsedContentOfFile2[$key];
            $diff[] = "- {$key}: {$value1}";
            $diff[] = "+ {$key}: {$value2}";
        } else {
            $value = is_bool($parsedContentOfFile1[$key]) ? var_export($parsedContentOfFile1[$key], true) : $parsedContentOfFile1[$key];
            $diff[] = "  {$key}: {$value}";
        }
    }

    return "{\n" . implode("\n", $diff) . "\n}";
}

