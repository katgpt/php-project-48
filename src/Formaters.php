<?php

namespace PhpProject48\Src\Formaters;

use function PhpProject48\Src\Formaters\Plain\plain;
use function PhpProject48\Src\Formaters\Stylish\stylish;
use function PhpProject48\Src\Formaters\Json\json;

/**
 * Function formats the difference array of two files
 * for displaying on the screen.
 *
 * @param array<mixed> $differences difference array of two files
 * @param string $formatter 'plain' or 'stylish'
 *
 * @return string for displaying on the screen
 */
function format(array $differences, string $formatter): string
{
    return match ($formatter) {
        'stylish' => stylish($differences),
        'plain' => plain($differences),
        'json' => json($differences),
        default => throw new \Exception("Error: There is no such '{$formatter}' formatter!")
    };
}