<?php

namespace Differ\Formaters\Json;

function json(array $nodes): string
{
    $result = (string) json_encode($nodes);

    return "{$result}\n";
}
