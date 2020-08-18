<?php

namespace Parcom\Bytes\Streaming;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;
use Parcom\Needed;

function is_a(string $arr): callable
{
    return function (Input $input) use ($arr) {
        $errKind = ErrorKind::IsA();
        return $input->split_at_position1(fn($c) => strpos($arr, $c) === false, $errKind);
    };
}

function is_not(string $arr): callable
{
    return function (Input $input) use ($arr) {
        $errKind = ErrorKind::IsA();
        return $input->split_at_position1(fn($c) => strpos($arr, $c) !== false, $errKind);
    };
}

function tag(string $tag): callable
{
    return function (Input $input) use ($tag): IResult {
        $tagLength = strlen($tag);
        $inputLength = $input->input_length();
        if ($tagLength > $inputLength) {
            return IResult::Err(Err::Incomplete(Needed::Size($tagLength - $inputLength)));
        }
        [$remaining, $output] = $input->take_split($tagLength);
        if ($tag == (string)$output) {
            return IResult::Ok($remaining, $output);
        }
        return IResult::Err(Err::Error($input, ErrorKind::Tag()));
    };
}

function tag_no_case(string $tag): callable
{
    return function (Input $input) use ($tag): IResult {
        $tagLength = strlen($tag);
        $inputLength = $input->input_length();
        if ($tagLength > $inputLength) {
            return IResult::Err(Err::Incomplete(Needed::Size($tagLength - $inputLength)));
        }
        [$remaining, $output] = $input->take_split($tagLength);
        if (strtolower((string)$tag) == strtolower((string)$output)) {
            return IResult::Ok($remaining, $output);
        }
        return IResult::Err(Err::Error($input, ErrorKind::Tag()));
    };
}

function take_until(Input $tag): callable
{
    return function (Input $input) use ($tag): IResult {
        $offset = $input->find_substring($tag);
        if ($offset == -1) {
            return IResult::Err(Err::Incomplete(Needed::Size($tag->input_length())));
        }
        return IResult::Ok(...$input->take_split($offset));
    };
}

function take_till(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position($cond);
    };
}

function take_till1(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position1($cond, ErrorKind::TakeTill1());
    };
}

function take_while(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position(fn($c) => !$cond($c));
    };
}

function take_while1(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position1(fn($c) => !$cond($c), ErrorKind::TakeWhile1());
    };
}

function take_while_m_n(int $m, int $n, callable $cond): callable
{
    return function (Input $input) use ($m, $n, $cond): IResult {
        $idx = $input->position(fn($c) => !$cond($c));
        if ($idx == -1) {
            $inputLength = $input->input_length();
            if ($inputLength >= $n) {
                return IResult::Ok(...$input->take_split($n));
            } else {
                $needed = $m > $inputLength ? $m - $inputLength : 1;
                return IResult::Err(Err::Incomplete(Needed::Size($needed)));
            }
        } else {
            if ($idx >= $m) {
                if ($idx <= $n) {
                    return IResult::Ok(...$input->take_split($idx));
                } else {
                    return IResult::Ok(...$input->take_split($n));
                }
            } else {
                return IResult::Err(Err::Error($input, ErrorKind::TakeWhileMN()));
            }
        }
    };
}
