<?php

namespace Parcom\Character\Streaming;

use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;
use function Parcom\Character\is_alphabetic;
use function Parcom\Character\is_digit;

function alpha0(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position(fn($c) => !is_alphabetic($c));
    };
}

function alpha1(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position1(fn($c) => !is_alphabetic($c), ErrorKind::Alpha());
    };
}


function digit0(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position(fn($c) => !is_digit($c));
    };
}
