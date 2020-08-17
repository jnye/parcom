<?php

namespace Parcom\Bytes\Streaming;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;
use Parcom\Needed;

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
