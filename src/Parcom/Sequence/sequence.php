<?php

namespace Parcom\Sequence;

use Parcom\Input;
use Parcom\IResult;

function delimited(callable $first, callable $middle, callable $last): callable
{
    return function (Input $input) use ($first, $middle, $last): IResult {
        $result = $first($input);
        if ($result->is_err()) {
            return $result;
        }
        $middleResult = $middle($result[0]);
        if ($middleResult->is_err()) {
            return $middleResult;
        }
        $result = $last($middleResult[0]);
        if ($result->is_err()) {
            return $result;
        }
        return IResult::Ok($result[0], $middleResult[1]);
    };
}
