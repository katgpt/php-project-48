<?php

namespace PhpProject48\Src\Differ;

function genDiff(string $filepath1, string $filepath2, string $formatName = 'stylish'): string
{
    $file1 = json_decode(file_get_contents($filepath1), true);
    $file2 = json_decode(file_get_contents($filepath2), true);

    $keys = array_unique(array_merge(array_keys($file1), array_keys($file2)));
    sort($keys);

    $diff = [];
    foreach ($keys as $key) {
        if (!array_key_exists($key, $file1)) {
            $value = is_bool($file2[$key]) ? var_export($file2[$key], true) : $file2[$key];
            $diff[] = "+ {$key}: {$value}";
        } elseif (!array_key_exists($key, $file2)) {
            $value = is_bool($file1[$key]) ? var_export($file1[$key], true) : $file1[$key];
            $diff[] = "- {$key}: {$value}";
        } elseif ($file1[$key] !== $file2[$key]) {
            $value1 = is_bool($file1[$key]) ? var_export($file1[$key], true) : $file1[$key];
            $value2 = is_bool($file2[$key]) ? var_export($file2[$key], true) : $file2[$key];
            $diff[] = "- {$key}: {$value1}";
            $diff[] = "+ {$key}: {$value2}";
        } else {
            $value = is_bool($file1[$key]) ? var_export($file1[$key], true) : $file1[$key];
            $diff[] = "  {$key}: {$value}";
        }
    }

    return "{\n" . implode("\n", $diff) . "\n}";
}