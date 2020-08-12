<?php

namespace Combinator;

use Parcom\Bytes;
use Parcom\Combinator;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Combinator::opt
 */
class OptTest extends TestCase
{

    public function testOptSuccess()
    {
        $input = new Span("abc");
        $parser = Combinator::opt(Bytes::tag("ab"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("ab", $output);
        self::assertEquals("c", $remaining);
    }

    public function testOptFailure()
    {
        $input = new Span("abc");
        $parser = Combinator::opt(Bytes::tag("bc"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertNull($output);
        self::assertEquals("abc", $remaining);
    }

}