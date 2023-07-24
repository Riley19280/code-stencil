<?php

namespace CodeStencil\Utility;

function array_flatten(array $array): array
{
    $return = [];
    array_walk_recursive($array, function($a) use (&$return) {
        $return[] = $a;
    });

    return $return;
}
