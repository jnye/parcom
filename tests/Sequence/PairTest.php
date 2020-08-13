<?php

namespace Sequence;

use Parcom\Bytes;
use Parcom\Error;
use Parcom\Sequence;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Sequence::pair
 */
class PairTest extends TestCase
{

    public function testSequenceSimple()
    {
        $input = new Span("aircraft");
        $parser = Sequence::pair(
            Bytes::tag("air"),
            Bytes::tag("craft")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("air", $outputs[0]);
        self::assertEquals("craft", $outputs[1]);
        self::assertEquals("", $remaining);
    }

    public function testSequenceFirstFailure()
    {
        $input = new Span("aircraft");
        $parser = Sequence::pair(
            Bytes::tag("AIR"),
            Bytes::tag("craft")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testSequenceSecondFailure()
    {
        $input = new Span("aircraft");
        $parser = Sequence::pair(
            Bytes::tag("air"),
            Bytes::tag("CRAFT")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

}