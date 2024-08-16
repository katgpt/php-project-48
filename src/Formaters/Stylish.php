<?php

namespace Differ\Formaters\Stylish;

const NUMBER_INDENT_PER_LEVEL_FOR_TEXT = 4;
const NUMBER_INDENT_PER_LEVEL_FOR_BRACKETS = 2;
const SYMBOL_OF_INDENT = ' ';
const FOR_BRACKETS = true;

function stylish(array $nodes): string
{
    $result = format($nodes);

    return "{$result}\n";
}

function format(array $nodes, int $level = 1): string
{
    $result = array_reduce(
        $nodes,
        function ($carry, $item) use ($level) {
            $indent = getIndent($level);

            if ($item['type'] === 'changed') {
                $value1 = getChangedValue($item, 'value1', $level);
                $value2 = getChangedValue($item, 'value2', $level);

                return implode(
                    '',
                    [$carry,
                    "{$indent}- {$item['name']}: {$value1}\n",
                    "{$indent}+ {$item['name']}: {$value2}\n"]
                );
            }
            $value = getValue($item, $level);
            $prefix = getPrefix($item['type']);

            return implode(
                '',
                [$carry,
                "{$indent}{$prefix} {$item['name']}: {$value}\n"]
            );
        },
        ''
    );
    $indent = getIndent($level, FOR_BRACKETS);

    return "{\n{$result}{$indent}}";
}

function getFormatArray(array $array, int $level): string
{
    $listKeys = array_keys($array);

    $string = array_reduce(
        $listKeys,
        function ($carry, $item) use ($array, $level) {
            if (is_array($array[$item])) {
                $value = getFormatArray($array[$item], $level + 1);
            } else {
                $value = $array[$item];
            }
            $indent = getIndent($level);

            return implode(
                '',
                [ $carry,
                "{$indent}  {$item}: {$value}\n"]
            );
        },
        ''
    );
    $indent = getIndent($level, FOR_BRACKETS);

    return "{\n{$string}{$indent}}";
}

function getIndent(int $level, bool $isBrackets = false): string
{
    $indentToLeft = $isBrackets ?
        NUMBER_INDENT_PER_LEVEL_FOR_TEXT :
        NUMBER_INDENT_PER_LEVEL_FOR_BRACKETS;
    $indent = $level * NUMBER_INDENT_PER_LEVEL_FOR_TEXT - $indentToLeft;

    return str_repeat(SYMBOL_OF_INDENT, $indent);
}

function getPrefix(string $itemType): string
{
    return match ($itemType) {
        'unchanged' => ' ',
        'deleted' => '-',
        'added' => '+',
        default => throw new \Exception(
            "Error: Unknown property state type - '{$itemType}'!"
        )
    };
}

function getValue(array $node, int $level): mixed
{
    if (array_key_exists('children', $node)) {
        return format($node['children'], $level + 1);
    } elseif (array_key_exists('value', $node)) {
        if (is_array($node['value'])) {
            return getFormatArray($node['value'], $level + 1);
        }
    }

    return $node['value'];
}

function getChangedValue(array $node, string $keyValue, int $level): mixed
{
    if (is_array($node[$keyValue])) {
        $value = getFormatArray($node[$keyValue], $level + 1);
    } else {
        $value = $node[$keyValue];
    }

    return (is_bool($value) || is_null($value)) ?
        strtolower(var_export($value, true)) :
        $value;
}
