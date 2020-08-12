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

    public static function peek(callable $parser): callable
    {
        return function (Span $input) use ($parser) {
            [, $output, $err] = $parser($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            return [$input, $output, null];
        };
    }

    public static function opt(callable $parser): callable
    {
        return function (Span $input) use ($parser) {
            [$remaining, $output, $err] = $parser($input);
            if ($err !== null) {
                return [$input, null, null];
            }
            return [$remaining, $output, null];
        };
    }

    public static function recognize(callable $parser): callable
    {
        return function (Span $input) use ($parser) {
            [$remaining, $output, $err] = $parser($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            $output = new Span($input->input(), $input->offset(), $output->offset() + $output->length());
            return [$remaining, $output, null];
        };
    }

}