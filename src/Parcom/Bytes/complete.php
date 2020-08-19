<?php

namespace Parcom\Bytes\Complete;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;

function is_a(string $arr): callable
{
    return function (Input $input) use ($arr) {
        $errKind = ErrorKind::IsA();
        return $input->split_at_position1_complete(fn($c) => strpos($arr, $c) === false, $errKind);
    };
}

function is_not(string $arr): callable
{
    return function (Input $input) use ($arr) {
        $errKind = ErrorKind::IsNot();
        return $input->split_at_position1_complete(fn($c) => strpos($arr, $c) !== false, $errKind);
    };
}

function tag(string $tag): callable
{
    return function (Input $input) use ($tag): IResult {
        $tagLength = strlen($tag);
        if ($tagLength > $input->input_length()) {
            return IResult::Err(Err::Error($input, ErrorKind::Tag()));
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
            return IResult::Err(Err::Error($input, ErrorKind::Tag()));
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
            return IResult::Err(Err::Error($input, ErrorKind::TakeUntil()));
        }
        return IResult::Ok(...$input->take_split($offset));
    };
}

function take_till(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position_complete($cond);
    };
}

function take_till1(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position1_complete($cond, ErrorKind::TakeTill1());
    };
}
