<?php

namespace Bytes;

use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Bytes::isA
 */
class IsATest extends TestCase
{

    public function testIsASuccess()
    {
        $input = new Span("012abc");
        $parser = Bytes::isA("210cba");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("012abc", $output);
        self::assertEquals("", $remaining);
    }

    public function testIsARemainingSuccess()
    {
        $input = new Span("012abcdef");
        $parser = Bytes::isA("210cba");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("012abc", $output);
        self::assertEquals("def", $remaining);
    }

    public function testIsAMatchFailure()
    {
        $input = new Span("def");
        $parser = Bytes::isA("210cba");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Error::ERR_IS_A, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testIsAEofFailure()
    {
        $input = new Span("");
        $parser = Bytes::isA("210cba");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}