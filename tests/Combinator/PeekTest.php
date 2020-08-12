<?php

namespace Combinator;

use Parcom\Bytes;
use Parcom\Combinator;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Combinator::peek
 */
class PeekTest extends TestCase
{

    public function testPeekSuccess()
    {
        $input = new Span("abc");
        $parser = Bytes::take(1);
        [$remaining, $output, $err] = Combinator::peek($parser)($input);
        self::assertNull($err);
        self::assertEquals("a", $output);
        self::assertEquals("abc", $remaining);
    }

    public function testPeekFailure()
    {
        $input = new Span("abc");
        $parser = Bytes::take(4);
        [$remaining, $output, $err] = Combinator::peek($parser)($input);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}