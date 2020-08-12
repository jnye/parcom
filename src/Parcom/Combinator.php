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

    public static function map(callable $parser, callable $mapper): callable
    {
        return function (Span $input) use ($parser, $mapper) {
            [$remaining, $output, $err] = $parser($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            return [$remaining, $mapper($output), null];
        };
    }

}