<?php

namespace Combinator;

use Parcom\Bytes;
use Parcom\Combinator;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Combinator::cond
 */
class CondTest extends TestCase
{

    public function testCondTrue()
    {
        $input = new Span("abc");
        $parser = Bytes::take(3);
        [$remaining, $output, $err] = Combinator::cond(true, $parser)($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("", $remaining);
    }

    public function testCondFalse()
    {
        $input = new Span("abc");
        $parser = Bytes::take(3);
        [$remaining, $output, $err] = Combinator::cond(false, $parser)($input);
        self::assertNull($err);
        self::assertNull($output);
        self::assertEquals("abc", $remaining);
    }

}