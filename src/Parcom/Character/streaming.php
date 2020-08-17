<?php

namespace Parcom\Character\Streaming;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;
use Parcom\Needed;
use function Parcom\Character\is_alphabetic;

function alpha0(): callable
{
    return function (Input $input): IResult {
        $eof = false;
        $count = 0;
        do {
            $matched = false;
            if ($input->input_length() == 0) {
                $eof = true;
                break;
            }
            if (is_alphabetic($input[$count])) {
                $count++;
                $matched = true;
            }
            if ($count >= $input->input_length()) {
                $eof = true;
                break;
            }
        } while ($matched);
        if ($eof && $count == 0) {
            return IResult::Err(Err::Incomplete(Needed::Unknown()));
        }
        [$remaining, $output] = $input->take_split($count);
        return IResult::Ok($remaining, $output);
    };
}

function alpha1(): callable
{
    return function (Input $input): IResult {
        $eof = false;
        $count = 0;
        do {
            $matched = false;
            if ($input->input_length() == 0) {
                $eof = true;
                break;
            }
            if (is_alphabetic($input[$count])) {
                $count++;
                $matched = true;
            }
            if ($count >= $input->input_length()) {
                $eof = true;
                break;
            }
        } while ($matched);
        if ($eof && $count == 0) {
            return IResult::Err(Err::Incomplete(Needed::Unknown()));
        }
        if ($count == 0) {
            return IResult::Err(Err::Error($input, ErrorKind::Alpha()));
        }
        [$remaining, $output] = $input->take_split($count);
        return IResult::Ok($remaining, $output);
    };
}
