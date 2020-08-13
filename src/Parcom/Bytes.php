<?php

namespace Parcom;

class Bytes
{

    /**
     * @param int $count
     * @return array
     */
    public static function take(int $count): callable
    {
        return function (Span $input) use ($count): array {
            if ($input->length() < $count) {
                return [null, null, "Err::Eof"];
            }
            return [$input->span($count), $input->span(0, $count), null];
        };
    }

    public static function tag(string $tag): callable
    {
        return function (Span $input) use ($tag): array {
            $tagLen = strlen($tag);
            if ($input->length() < $tagLen) {
                return [null, null, "Err::Eof"];
            }
            $peek = $input->span(0, $tagLen);
            if ($tag == $peek) {
                return [$input->span($tagLen), $peek, null];
            }
            return [null, null, Error::ERR_TAG];
        };
    }

    public static function tagNoCase(string $tag): callable
    {
        return function (Span $input) use ($tag): array {
            $tagLen = strlen($tag);
            if ($input->length() < $tagLen) {
                return [null, null, "Err::Eof"];
            }
            $peek = $input->span(0, $tagLen);
            if (strcasecmp($tag, $peek) === 0) {
                return [$input->span($tagLen), $peek, null];
            }
            return [null, null, Error::ERR_TAG];
        };
    }

    public static function isA(string $matches): callable
    {
        return function (Span $input) use ($matches): array {
            $inputLength = $input->length();
            if ($inputLength === 0) {
                return [null, null, Error::ERR_EOF];
            }
            $count = 0;
            for ($i = 0; $i < $inputLength; $i++) {
                if (strpos($matches, $input[$i]) === false) {
                    break;
                }
                $count++;
            }
            if ($count == 0) {
                return [null, null, Error::ERR_IS_A];
            }
            return [$input->span($count), $input->span(0, $count), null];
        };
    }

}