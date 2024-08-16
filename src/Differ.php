<?php

namespace Differ\Differ;

use Exception;

use function Differ\Formaters\format;
use function Differ\Parsers\parse;

function genDiff(string $pathFirst, string $pathSecond, string $formatter = 'stylish'): string
{
    [$firstFileRawContents, $firstFileFormat] = getFileContents($pathFirst);
    $firstFileContents = parse($firstFileRawContents, $firstFileFormat);

    [$secondFileRawContents, $secondFileFormat] = getFileContents($pathSecond);
    $secondFileContents = parse($secondFileRawContents, $secondFileFormat);

    $differences = getDifference($firstFileContents, $secondFileContents);
    $outputDiff = format($differences, $formatter);

    return $outputDiff;
}

function getFileContents(string $filepath): array
{
    if (!is_readable($filepath)) {
        throw new \Exception("Error: The file '{$filepath}' do not exist or are unreadable!\n");
    }
    $format = match (pathinfo($filepath, PATHINFO_EXTENSION)) {
        'json' => 'json',
        'yaml', 'yml' => 'yaml',
        default => throw new \Exception(
            "Error: Invalid file extension, use json- or yaml/yml- files!\n"
        )
    };
    $content = (string) file_get_contents($filepath);

    return [$content, $format];
}

function getDifference(object $firstStructure, object $secondStructure): array
{
    return array_reduce(
        getSortedListAllKeys($firstStructure, $secondStructure),
        function ($carry, $item) use ($firstStructure, $secondStructure) {
            $firstStructureKeyExists = property_exists($firstStructure, (string) $item);
            $secondStructureKeyExists = property_exists($secondStructure, (string) $item);

            switch (true) {
                case $firstStructureKeyExists && $secondStructureKeyExists:
                    if (is_object($firstStructure->$item) && is_object($secondStructure->$item)) {
                        $nestedStructure = getDifference($firstStructure->$item, $secondStructure->$item);
                        $result = array_merge($carry, [getNodeWithOneValue($item, [$nestedStructure], 'unchanged')]);
                    } elseif ($firstStructure->$item === $secondStructure->$item) {
                        $result = array_merge(
                            $carry,
                            [getNodeWithOneValue($item, [$firstStructure->$item], 'unchanged')]
                        );
                    } else {
                        $result = array_merge(
                            $carry,
                            [getNodeWithTwoValues(
                                $item,
                                [$firstStructure->$item, $secondStructure->$item],
                                'changed'
                            )]
                        );
                    }
                    break;
                case !$secondStructureKeyExists && $firstStructureKeyExists:
                    $result = array_merge(
                        $carry,
                        [getNodeWithOneValue(
                            $item,
                            [$firstStructure->$item],
                            'deleted'
                        )]
                    );
                    break;
                case !$firstStructureKeyExists && $secondStructureKeyExists:
                    $result = array_merge(
                        $carry,
                        [getNodeWithOneValue(
                            $item,
                            [$secondStructure->$item],
                            'added'
                        )]
                    );
                    break;
                default:
                    throw new \Exception("Error: unforeseen variant of comparing two structures!\n");
            }

            return $result;
        },
        []
    );
}

function getKeysOfStructure(object $structure): array
{
    return array_keys(json_decode((string) json_encode($structure), true));
}

function sortArray(array $array): array
{
    if (count($array) > 1) {
        $minItem = min($array);
        $subArray = array_filter($array, fn($item) => $item !== $minItem);

        return array_merge([$minItem], sortArray($subArray));
    }

    return $array;
}

function getSortedListAllKeys(object $firstTree, object $secondTree): array
{
    $firstStructureKeys = getKeysOfStructure($firstTree);
    $secondStructureKeys = getKeysOfStructure($secondTree);
    $listAllKeys = array_unique(array_merge($firstStructureKeys, $secondStructureKeys));

    return sortArray($listAllKeys);
}

function getChildTree(mixed $treeItem): array
{
    return json_decode((string) json_encode($treeItem), true);
}

function getNodeWithOneValue(int|string $name, array $values, string $type): array
{
    [$value] = $values;
    if (is_array($value)) {
        $children = getChildTree($value);

        return [
            'name' => $name,
            'type' => $type,
            'children' => $children
        ];
    }
    if (is_object($value)) {
        $value2 = getChildTree($value);
    } else {
        $value2 = (is_bool($value) || is_null($value)) ?
        strtolower(var_export($value, true)) :
        $value;
    }

    return [
        'name' => $name,
        'type' => $type,
        'value' => $value2
    ];
}

function getNodeWithTwoValues(int|string $name, array $values, string $type): array
{
    return [
        'name' => $name,
        'type' => $type,
        'value1' => $values[0],
        'value2' => $values[1]
    ];
}
