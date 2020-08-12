<?php

namespace Parcom;

class Combinator
{

    public static function cond(bool $bool, callable $parser): callable
    {
        return function (Span $input) use ($bool, $parser): array {
            if ($bool) {
                return $parser($input);
            }
            return [$input, null, null];
        };
    }

}