<?php

namespace Parcom;

class Character
{

    public static function digit0(): callable
    {
        return static::zeroOrMore([static::class, 'is_digit']);
    }

    public static function alpha0(): callable
    {
        return static::zeroOrMore([static::class, 'is_alphabetic']);
    }

    public static function alphanumeric0(): callable
    {
        return static::zeroOrMore([static::class, 'is_alphanumeric']);
    }

    public static function space0(): callable
    {
        return static::zeroOrMore([static::class, 'is_space']);
    }

    public static function is_space(string $char): bool
    {
        return $char === ' ' || $char === "\t";
    }

    public static function is_digit(string $char): bool
    {
        return ord($char) >= ord('0') && ord($char) <= ord('9');
    }

    public static function is_alphabetic(string $char): bool
    {
        return
            (ord($char) >= ord('a') && ord($char) <= ord('z'))
            || (ord($char) >= ord('A') && ord($char) <= ord('Z'));
    }

    public static function is_alphanumeric(string $char): bool
    {
        return static::is_alphabetic($char) || static::is_digit($char);
    }

    public static function digit1(): callable
    {
        return static::oneOrMore([static::class, 'is_digit'], Error::ERR_DIGIT);
    }

    public static function alpha1(): callable
    {
        return static::oneOrMore([static::class, 'is_alphabetic'], Error::ERR_ALPHABETIC);
    }

    public static function alphanumeric1(): callable
    {
        return static::oneOrMore([static::class, 'is_alphanumeric'], Error::ERR_ALPHANUMERIC);
    }

    public static function space1(): callable
    {
        return static::oneOrMore([static::class, 'is_space'], Error::ERR_SPACE);
    }

    private static function oneOrMore(array $userFunction, string $err): callable
    {
        return function (Span $input) use ($userFunction, $err): array {
            $count = 0;
            $offset = $input->offset();
            $max = $offset + $input->length() - 1;
            while ($offset <= $max && call_user_func($userFunction, $input[$count])) {
                $offset++;
                $count++;
            }
            if ($count == 0) {
                return [null, null, $err];
            }
            return [$input->span($count), $input->span(0, $count), null];
        };
    }

    private static function zeroOrMore(array $userFunction): callable
    {
        return function (Span $input) use ($userFunction): array {
            $count = 0;
            $offset = $input->offset();
            $max = $offset + $input->length();
            while ($offset < $max && call_user_func($userFunction, $input[$count])) {
                $offset++;
                $count++;
            }
            return [$input->span($count), $input->span(0, $count), null];
        };
    }
}

