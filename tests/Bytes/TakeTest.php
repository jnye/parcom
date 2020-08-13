<?php

namespace Bytes;

use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Bytes::take
 */
class TakeTest extends TestCase
{

    public function testTakeZero()
    {
        $span = new Span("abc");
        $result = Bytes::take(0)($span);
        [$remaining, $output, $err] = $result;
        self::assertNull($err);
        self::assertEquals("abc", $remaining);
        self::assertEquals("", $output);
    }

    public function testTakeOne()
    {
        $span = new Span("abc");
        $result = Bytes::take(1)($span);
        [$input, $output] = $result;
        self::assertEquals("bc", $input);
        self::assertEquals("a", $output);
    }

    public function testTakeTwo()
    {
        $span = new Span("abc");
        $result = Bytes::take(2)($span);
        [$input, $output] = $result;
        self::assertEquals("c", $input);
        self::assertEquals("ab", $output);
    }

    public function testTakeThree()
    {
        $span = new Span("abc");
        $result = Bytes::take(3)($span);
        [$input, $output] = $result;
        self::assertEquals("", $input);
        self::assertEquals("abc", $output);
    }

    public function testTakeErrEof()
    {
        $span = new Span("abc");
        $result = Bytes::take(4)($span);
        [$input, $output, $err] = $result;
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertEquals(null, $input);
        self::assertEquals(null, $output);
    }

}