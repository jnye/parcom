<?php

namespace Parcom;

class Sequence
{

    public static function pair(callable $first, callable $second): callable
    {
        return function ($input) use ($first, $second): array {
            [$remaining, $outputFirst, $err] = $first($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            $input = $remaining;
            [$remaining, $outputSecond, $err] = $second($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            return [$remaining, [$outputFirst, $outputSecond], null];
        };
    }

    public static function preceded(callable $first, callable $second): callable
    {
        return function ($input) use ($first, $second): array {
            [$remaining, $outputs, $err] = self::pair($first, $second)($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            return [$remaining, $outputs[1], null];
        };
    }

    public static function terminated(callable $first, callable $second): callable
    {
        return function (Span $input) use ($first, $second): array {
            [$remaining, $output, $err] = $first($input);
            if ($err !== null) {
                return [null, null, $err];
            }
            [$remaining, , $err] = $second($remaining);
            if ($err !== null) {
                return [null, null, $err];
            }
            return [$remaining, $output, null];
        };
    }

}