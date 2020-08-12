<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Parcom\Branch;
use Parcom\Bytes;
use Parcom\Character;
use Parcom\Combinator;
use Parcom\Sequence;
use Parcom\Span;

$oneOrMoreDigits = Character::digit1();

$optionalSign = Combinator::opt(
    Branch::alt([
        Bytes::tag("+"),
        Bytes::tag("-")
    ])
);

$integer = Combinator::recognize(
    Sequence::preceded($optionalSign, $oneOrMoreDigits)
);

[$remaining, $output, $err] = $integer(new Span("314"));
print "Found an integer: " . (string)$output . "\n";

[$remaining, $output, $err] = $integer(new Span("-314"));
print "Found an integer: " . (string)$output . "\n";

[$remaining, $output, $err] = $integer(new Span("+314"));
print "Found an integer: " . (string)$output . "\n";

$decimalPoint = Bytes::tag(".");

$unsignedReal = Combinator::recognize(
    Sequence::preceded(
        Sequence::preceded(
            Combinator::opt(
                $oneOrMoreDigits
            ),
            $decimalPoint
        ),
        $oneOrMoreDigits
    )
);

$real = Combinator::recognize(
    Sequence::preceded($optionalSign, $unsignedReal)
);

[$remaining, $output, $err] = $real(new Span("3.14"));
print "Found an real: " . (string)$output . "\n";

[$remaining, $output, $err] = $real(new Span("-3.14"));
print "Found an real: " . (string)$output . "\n";

[$remaining, $output, $err] = $real(new Span("+3.14"));
print "Found an real: " . (string)$output . "\n";
