<?php

namespace Bytes;

use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Bytes::tag
 * @covers \Parcom\Bytes::tagNoCase
 */
class TagTest extends TestCase
{

    public function testTagSimpleMatch()
    {
        $span = new Span("a");
        [$input, $output, $err] = Bytes::tag("a")($span);
        self::assertNull($err);
        self::assertEquals("a", $output);
        self::assertEquals("", $input);
    }

    public function testTagSimpleMismatch()
    {
        $span = new Span("b");
        [$input, $output, $err] = Bytes::tag("a")($span);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($input);
        self::assertNull($output);
    }

    public function testTagSimpleEof()
    {
        $span = new Span("");
        [$input, $output, $err] = Bytes::tag("a")($span);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertNull($input);
        self::assertNull($output);
    }

    public function testTagNoCaseSimpleMatch()
    {
        $span = new Span("aBc");
        [$remaining, $output, $err] = Bytes::tagNoCase("abc")($span);
        self::assertNull($err);
        self::assertEquals("aBc", $output);
        self::assertEquals("", $remaining);
    }

    public function testTagNoCaseMatchFailure()
    {
        $span = new Span("DeF");
        [$remaining, $output, $err] = Bytes::tagNoCase("abc")($span);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTagNoCaseEofFailure()
    {
        $span = new Span("");
        [$remaining, $output, $err] = Bytes::tagNoCase("abc")($span);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}