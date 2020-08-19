<?php

namespace Parcom\Character\Complete;

use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;
use function Parcom\Character\is_alphabetic;
use function Parcom\Character\is_digit;

function alpha0(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position_complete(fn($c) => !is_alphabetic($c));
    };
}

function alpha1(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position1_complete(fn($c) => !is_alphabetic($c), ErrorKind::Alpha());
    };
}


function digit0(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position_complete(fn($c) => !is_digit($c));
    };
}

function digit1(): callable
{
    return function (Input $input): IResult {
        return $input->split_at_position1_complete(fn($c) => !is_digit($c), ErrorKind::Digit());
    };
}
