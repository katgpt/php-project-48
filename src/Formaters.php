<?php

namespace Differ\Formaters;

use function Differ\Formaters\Plain\plain;
use function Differ\Formaters\Stylish\stylish;
use function Differ\Formaters\Json\json;

function format(array $differences, string $formatter): string
{
    return match ($formatter) {
        'stylish' => stylish($differences),
        'plain' => plain($differences),
        'json' => json($differences),
        default => throw new \Exception("Error: There is no such '{$formatter}' formatter!")
    };
}
