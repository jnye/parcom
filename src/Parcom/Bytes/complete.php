<?php

namespace Parcom\Bytes\Complete;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;

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