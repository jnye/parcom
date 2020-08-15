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

function pair(callable $first, callable $second): callable
{
    return function (Input $input) use ($first, $second): IResult {
        $firstResult = $first($input);
        if ($firstResult->is_err()) {
            return $firstResult;
        }
        $secondResult = $second($firstResult[0]);
        if ($secondResult->is_err()) {
            return $secondResult;
        }
        return IResult::Ok($secondResult[0], $firstResult[1], $secondResult[1]);
    };
}

function preceded(callable $first, callable $second): callable
{
    return function (Input $input) use ($first, $second): IResult {
        $firstResult = $first($input);
        if ($firstResult->is_err()) {
            return $firstResult;
        }
        return $second($firstResult[0]);
    };
}
