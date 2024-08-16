<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $fileContent, string $format): object
{
    return match ($format) {
        'json' => json_decode($fileContent, false),
        'yaml' => Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new \Exception(
            "Error: Invalid file extension '{$format}', use json- or yaml/yml- files !\n"
        )
    };
}
