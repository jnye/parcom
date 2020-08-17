<?php

namespace Parcom\Character;

function is_alphabetic(string $c): bool
{
    $n = ord($c);
    return ($n >= 0x41 && $n <= 0x5a)
        || ($n >= 0x61 && $n <= 0x7a);
}

function is_digit(string $c): bool
{
    $n = ord($c);
    return ($n >= 0x30 && $n <= 0x39);
}
