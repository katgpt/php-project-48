<?php

namespace Differ\Differ;

use Exception;

use function Differ\Formaters\format;
use function Differ\Parsers\parse;

/**
 * Function genDiff is constructed based on how the files have changed
 * relative to each other, the keys are output in alphabetical order.
 *
 * @param string $pathFirst  path to first file
 * @param string $pathSecond path to second file
 * @param string $formatter style formating
 *
 * @return string file differences in relation to each other
 */
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

/**
 * Function receives the JSON or YML/YAML file content and decodes it into an object
 *
 * @param string $filepath path to JSON-file
 *
 * @return array<string>
 */
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

/**
 * Function compares two files (JSON or YML|YAML) and creates an array of differences for further formatting
 *
 * @param object $firstStructure original object, before changes;
 * @param object $secondStructure final object, after changes;
 *
 * @return array<mixed> array like this:
 * [
 *  'name'  => '<name of object's property>',
 *  'value' => '<value of object's property>',
 *  'type'  => 'unchanged | deleted | added'
 * ] or
 * [
 *  'name'  => '<name of object's property>',
 *  'value1' => '<first value of object's property>',
 *  'value2' => '<second value of object's property>',
 *  'type'  => 'changed'
 * ]
 */
function getDifference(object $firstStructure, object $secondStructure): array
{
    return array_reduce(
        getSortedListAllKeys($firstStructure, $secondStructure),
        function ($carry, $item) use ($firstStructure, $secondStructure) {
            $firstStructureKeyExists = property_exists($firstStructure, (string) $item);
            $secondStructureKeyExists = property_exists($secondStructure, (string) $item);

            switch (true) {
                case $firstStructureKeyExists && $secondStructureKeyExists:
                    if (is_object($firstStructure -> $item) && is_object($secondStructure -> $item)) {
                        $nestedStructure = getDifference($firstStructure -> $item, $secondStructure -> $item);
                        $result = array_merge($carry, [getNodeWithOneValue($item, [$nestedStructure], 'unchanged')]);
                    } elseif ($firstStructure -> $item === $secondStructure -> $item) {
                        $result = array_merge(
                            $carry,
                            [getNodeWithOneValue($item, [$firstStructure -> $item], 'unchanged')]
                        );
                    } else {
                        $result = array_merge(
                            $carry,
                            [getNodeWithTwoValues(
                                $item,
                                [$firstStructure -> $item, $secondStructure -> $item],
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
                            [$firstStructure -> $item],
                            'deleted'
                        )]
                    );
                    break;
                case !$firstStructureKeyExists && $secondStructureKeyExists:
                    $result = array_merge(
                        $carry,
                        [getNodeWithOneValue(
                            $item,
                            [$secondStructure -> $item],
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

/**
 * Function returned all keys of structure such object;
 *
 * @param object $structure object;
 *
 * @return array<int, int|string> all keys of object;
 */
function getKeysOfStructure(object $structure): array
{
    return array_keys(json_decode((string) json_encode($structure), true));
}

/**
 * Immutable array sorting function;
 *
 * @param array<int|string> $array sorted array;
 *
 * @return array<int|string> already sorted array;
 */
function sortArray(array $array): array
{
    if (count($array) > 1) {
        $minItem = min($array);
        $subArray = array_filter($array, fn($item) => $item !== $minItem);

        return array_merge([$minItem], sortArray($subArray));
    }

    return $array;
}

/**
 * The function returns a sorted list of all keys of passed structures (trees)
 *
 * @param object $firstTree first structure (tree);
 * @param object $secondTree second structure (tree);
 *
 * @return array<int|string> sorted list of all keys of passed structures (trees);
 */
function getSortedListAllKeys(object $firstTree, object $secondTree): array
{
    $firstStructureKeys = getKeysOfStructure($firstTree);
    $secondStructureKeys = getKeysOfStructure($secondTree);
    $listAllKeys = array_unique(array_merge($firstStructureKeys, $secondStructureKeys));

    return sortArray($listAllKeys);
}

/**
 * Function returns the child tree of the passed node
 *
 * @param mixed $treeItem item(node) of tree;
 *
 * @return array<mixed> child tree;
 */
function getChildTree(mixed $treeItem): array
{
    return json_decode((string) json_encode($treeItem), true);
}

/**
 * Function create node with name, value and type
 *
 * @param string $name is name node;
 * @param array<mixed> $values is array from one value node;
 * @param string $type is type node may be 'unchanged' | 'deleted' | 'added' ;
 *
 * @return array<mixed> return node;
 */
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

/**
 * Function create node with name, two values and type
 *
 * @param string $name is name node;
 * @param array<mixed> $values is array from old value and new value node;
 * @param string $type is type node may be 'changed';
 *
 * @return array<mixed> return node;
 */
function getNodeWithTwoValues(int|string $name, array $values, string $type): array
{
    return [
        'name' => $name,
        'type' => $type,
        'value1' => $values[0],
        'value2' => $values[1]
    ];
}
