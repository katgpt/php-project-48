<?php

namespace Differ\Formaters\Plain;

function plain(array $nodes, string $path = ''): string
{
    return array_reduce($nodes, function ($carry, $item) use ($path) {
        $nameNode = implode('', [$path, "{$item['name']}."]);

        if (array_key_exists('children', $item)) {
            return implode('', [$carry, plain($item['children'], $nameNode)]);
        }
        if ($item['type'] === 'deleted') {
            return getTextForProperty('deleted', rtrim($nameNode, '.'), $carry);
        }
        if ($item['type'] === 'added') {
                return getTextForProperty('added', rtrim($nameNode, '.'), $carry, getNormalizedValue($item));
        }
        if ($item['type'] === 'changed') {
            return getTextForProperty('changed', rtrim($nameNode, '.'), $carry, getNormalizedValue($item));
        }

        return $carry;
    }, '');
}

/**
 * The function returns the text for the property depending on
 * the specified type 'added' or 'deleted' or 'updated'
 *
 * @param string $type for choice of kind text
 * @param string $nameProperty
 * @param string $textAccumulator
 * @param array<mixed> $value array from old value and new value of property
 *
 * @return string
 */
function getTextForProperty(string $type, string $nameProperty, string $textAccumulator, array $value = []): string
{
    return match ($type) {
        'deleted' => implode(
            '',
            [$textAccumulator, "Property '{$nameProperty}' was removed\n"]
        ),
        'changed' => implode(
            '',
            [$textAccumulator, "Property '{$nameProperty}' was updated. From {$value[0]} to {$value[1]}\n"]
        ),
        'added' => implode(
            '',
            [$textAccumulator, "Property '{$nameProperty}' was added with value: {$value[0]}\n"]
        ),
        default => throw new \Exception("Error: There is no such state -'{$type}' for the properties being compared!\n")
    };
}

/**
 * Function returns [complex value] instead of the argument $value,
 * if the argument is an array or adds quotes to the argument
 * if the argument is not a number or one of the following values:
 * 'true', 'false', 'null'. Otherwise returns the argument as is.
 *
 * @param array<mixed> $node
 *
 * @return array<mixed>
 */
function getNormalizedValue(array $node): array
{
    if ($node['type'] === 'changed') {
        $value1 = getChangedValue($node, 'value1');
        $value2 = getChangedValue($node, 'value2');

        return [$value1, $value2];
    }
    $value = $node['value'];

    if (is_array($value)) {
        return ['[complex value]'];
    }

    if (
        !in_array($value, ['true', 'false', 'null'], true)
    ) {
        $normalizedValue = strtolower(var_export($value, true));

        return ["{$normalizedValue}"];
    }

    return [$value];
}

/**
 * Function returns one of tree node values depending on
 * passed key (‘value1’ | ‘value2’).
 *
 * @param array<mixed> $node node of tree
 * @param string $key key of values
 *
 * @return mixed normalized value from tree node with type
 * 'changed'
 */
function getChangedValue(array $node, $key): mixed
{
    if (is_array($node[$key])) {
        return '[complex value]';
    } elseif (
        !in_array($node[$key], ['true', 'false', 'null'], true)
    ) {
        $changedValue = strtolower(var_export($node[$key], true));

        return "{$changedValue}";
    }

    return $node[$key];
}
