<?php

namespace Differ\Formaters;

use function Differ\Formaters\Stylish\stylishFormat;
use function Differ\Formaters\Plain\plainFormat;
use function Differ\Formaters\Json\jsonFormat;

function makeFormat(array $diff, string $formatName): string
{
    return match ($formatName) {
        'stylish' => stylishFormat($diff),
        'plain' => plainFormat($diff),
        'json' => jsonFormat($diff),
        default => exit("Unknown format '{$formatName}'!\n")
    };
}