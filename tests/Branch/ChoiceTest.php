<?php

namespace Branch;

use Parcom\Branch;
use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Branch::choice
 */
class ChoiceTest extends TestCase
{

    public function testChoiceFirstWins()
    {
        $input = new Span("abc");
        $choice = Branch::choice([Bytes::take(2), Bytes::take(1)]);
        [$input, $output, $err] = $choice($input);
        self::assertEquals(null, $err);
        self::assertEquals("c", $input);
        self::assertEquals("ab", $output);
    }

    public function testChoiceSecondWins()
    {
        $input = new Span("a");
        $choice = Branch::choice([Bytes::take(2), Bytes::take(1)]);
        [$input, $output, $err] = $choice($input);
        self::assertEquals(null, $err);
        self::assertEquals("", $input);
        self::assertEquals("a", $output);
    }

    public function testChoiceFailure()
    {
        $input = new Span("a");
        $choice = Branch::choice([Bytes::take(3), Bytes::take(2)]);
        [$input, $output, $err] = $choice($input);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertEquals(null, $input);
        self::assertEquals(null, $output);
    }

}