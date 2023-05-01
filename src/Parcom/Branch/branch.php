<?php

namespace Parcom\Branch;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;

function alt(callable ...$parsers): callable
{
    return function (Input $input) use ($parsers) : IResult {
        $lastErr = null;
        foreach ($parsers as $parser) {
            $result = $parser($input);
            if ($result->is_ok()) {
                return $result;
            }
            $lastErr = $result->getErr();
        }
        return IResult::Err($lastErr);
    };
}

function permutation(callable ...$parsers): callable
{
    return function (Input $input) use ($parsers): IResult {
        $remaining = $input;
        $matchedResults = [];
        $matchCount = 0;
        do {
            $matchFound = false;
            foreach ($parsers as $idx => $parser) {
                if (isset($matchedResults[$idx])) {
                    continue;
                } else {
                    $matchedResults[$idx] = null;
                }
                $result = $parser($remaining);
                if ($result->is_err()) {
                    continue;
                }
                $matchFound = true;
                $matchCount++;
                $matchedResults[$idx] = $result[1];
                $remaining = $result[0];
            }
        } while ($matchFound);
        if ($matchCount < count($parsers)) {
            return IResult::Err(Err::Error($input, ErrorKind::Permutation()));
        }
        return IResult::Ok($remaining, ...$matchedResults);
    };
}
