<?php

namespace Sequence;

use Parcom\Bytes;
use Parcom\Error;
use Parcom\Sequence;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Sequence::preceded
 */
class PrecededTest extends TestCase
{

    public function testPrecededSimple()
    {
        $input = new Span("aircraft");
        $parser = Sequence::preceded(
            Bytes::tag("air"),
            Bytes::tag("craft")
        );
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("craft", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testPrecededFirstFailure()
    {
        $input = new Span("aircraft");
        $parser = Sequence::preceded(
            Bytes::tag("AIR"),
            Bytes::tag("craft")
        );
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}