<?php

namespace Bytes;

use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Bytes::tag
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

}