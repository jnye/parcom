<?php

namespace Parcom;

class Character
{

    public static function digit0(): callable
    {
        return function (Span $input): array {
            $count = 0;
            $offset = $input->offset();
            $max = $offset + $input->length();
            while ($offset < $max && self::is_digit($input[$offset])) {
                $offset++;
                $count++;
            }
            return [$input->span($count), $input->span(0, $count), null];
        };
    }

    public static function is_digit(string $char): bool
    {
        return ord($char) >= ord('0') && ord($char) <= ord('9');
    }

    public static function digit1(): callable
    {
        return function (Span $input): array {
            $count = 0;
            $offset = $input->offset();
            $max = $offset + $input->length() - 1;
            while ($offset <= $max && self::is_digit($input[$count])) {
                $offset++;
                $count++;
            }
            if ($count == 0) {
                return [null, null, Error::ERR_DIGIT];
            }
            return [$input->span($count), $input->span(0, $count), null];
        };
    }

}

