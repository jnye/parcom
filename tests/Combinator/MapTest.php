<?php

namespace Combinator;

use Parcom\Bytes;
use Parcom\Combinator;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Combinator::map
 */
class MapTest extends TestCase
{

    public function testMapSuccess()
    {
        $input = new Span("abc");
        $parser = Bytes::take(2);
        $mapper = function ($v) {
            return strlen($v);
        };
        [$remaining, $output, $err] = Combinator::map($parser, $mapper)($input);
        self::assertNull($err);
        self::assertEquals("c", $remaining);
        self::assertSame(2, $output);
    }

    public function testMapFailure()
    {
        $input = new Span("abc");
        $parser = Bytes::take(4);
        $mapper = function ($v) {
            return strlen($v);
        };
        [$remaining, $output, $err] = Combinator::map($parser, $mapper)($input);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertNull($remaining);
        self::assertNull($output);
    }

}