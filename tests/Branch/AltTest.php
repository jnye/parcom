<?php

namespace Branch;

use Parcom\Branch;
use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Branch::alt
 */
class AltTest extends TestCase
{

    public function testAltFirstWins()
    {
        $input = new Span("abc");
        $alt = Branch::alt([Bytes::take(2), Bytes::take(1)]);
        [$input, $output, $err] = $alt($input);
        self::assertEquals(null, $err);
        self::assertEquals("c", (string)$input);
        self::assertEquals("ab", (string)$output);
    }

    public function testAltSecondWins()
    {
        $input = new Span("a");
        $alt = Branch::alt([Bytes::take(2), Bytes::take(1)]);
        [$input, $output, $err] = $alt($input);
        self::assertEquals(null, $err);
        self::assertEquals("", (string)$input);
        self::assertEquals("a", (string)$output);
    }

    public function testAltFailure()
    {
        $input = new Span("a");
        $alt = Branch::alt([Bytes::take(3), Bytes::take(2)]);
        [$input, $output, $err] = $alt($input);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertEquals(null, $input);
        self::assertEquals(null, $output);
    }

}