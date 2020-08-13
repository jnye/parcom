<?php

namespace Parcom\Branch;

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
