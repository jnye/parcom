<?php

namespace Parcom\Bytes\Complete;

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\IResult;

function escaped(callable $normal, string $controlChar, callable $escapable): callable
{
    return function (Input $input) use ($normal, $controlChar, $escapable): IResult {
        $i = $input;
        while ($i->input_length() > 0) {
            [$remaining, , $err] = $normal($i);
            if ($err === null) {
                if ($remaining->input_length() == 0) {
                    return IResult::Err(Err::Error($remaining, ErrorKind::Escaped()));
                } else {
                    $i = $remaining;
                }
            } else {
                if ($i[0] == $controlChar) {
                    [$r,] = $i->take_split(1);
                    [$remaining, , $err] = $escapable($r);
                    if ($err === null) {
                        if ($remaining->input_length() == 0) {
                            return IResult::Ok(...$input->take_split($input->input_length()));
                        } else {
                            $i = $remaining;
                        }
                    } else {
                        return IResult::Err($err);
                    }
                } else {
                    $index = $input->offset($i);
                    return IResult::Ok(...$input->take_split($index));
                }
            }
        }
        return IResult::Ok(...$input->take_split($input->input_length()));
    };
}

function escaped_transform(callable $normal, string $controlChar, callable $transform): callable
{
    return function (Input $input) use ($normal, $controlChar, $transform): IResult {
        $i = $input;
        $res = new Input("");
        while ($i->input_length() > 0) {
            [$remaining, $output, $err] = $normal($i);
            if ($err === null) {
                $res->extend($output);
                if ($remaining->input_length() == 0) {
                    return IResult::Err(Err::Error($remaining, ErrorKind::Escaped()));
                } else {
                    $i = $remaining;
                }
            } else {
                if ($i[0] == $controlChar) {
                    [$r,] = $i->take_split(1);
                    [$remaining, $output, $err] = $transform($r);
                    if ($err === null) {
                        $res->extend($output);
                        if ($remaining->input_length() == 0) {
                            return IResult::Ok($remaining, $res);
                        } else {
                            $i = $remaining;
                        }
                    } else {
                        return IResult::Err($err);
                    }
                } else {
                    $index = $input->offset($i);
                    return IResult::Ok($input->take_split($index)[0], $res);
                }
            }
        }
        return IResult::Ok($input, $res);
    };
}

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

function take_while(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position_complete(fn($c) => !$cond($c));
    };
}

function take_while1(callable $cond): callable
{
    return function (Input $input) use ($cond): IResult {
        return $input->split_at_position1_complete(fn($c) => !$cond($c), ErrorKind::TakeWhile1());
    };
}

function take_while_m_n(int $m, int $n, callable $cond): callable
{
    return function (Input $input) use ($m, $n, $cond): IResult {
        $idx = $input->position(fn($c) => !$cond($c));
        if ($idx == -1) {
            $inputLength = $input->input_length();
            if ($inputLength >= $n) {
                return IResult::Ok(...$input->take_split($n));
            } else if ($inputLength >= $m) {
                return IResult::Ok(...$input->take_split($inputLength));
            } else {
                return IResult::Err(Err::Error($input, ErrorKind::TakeWhileMN()));
            }
        } else {
            if ($idx >= $m) {
                if ($idx <= $n) {
                    return IResult::Ok(...$input->take_split($idx));
                } else {
                    return IResult::Ok(...$input->take_split($n));
                }
            } else {
                return IResult::Err(Err::Error($input, ErrorKind::TakeWhileMN()));
            }
        }
    };
}
