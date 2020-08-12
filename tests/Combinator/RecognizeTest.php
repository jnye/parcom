<?php

namespace Combinator;

use Parcom\Bytes;
use Parcom\Combinator;
use Parcom\Error;
use Parcom\Sequence;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Combinator::recognize
 */
class RecognizeTest extends TestCase
{

    public function testRecognizeSuccess()
    {
        $input = new Span("pocketbooks");
        $parser = Sequence::preceded(
            Bytes::tag("pocket"),
            Bytes::tag("book")
        );
        [$remaining, $output, $err] = Combinator::recognize($parser)($input);
        self::assertNull($err);
        self::assertEquals("pocketbook", (string)$output);
        self::assertEquals("s", $remaining);
    }

    public function testRecognizeFailure()
    {
        $input = new Span("pockets");
        $parser = Sequence::preceded(
            Bytes::tag("pocket"),
            Bytes::tag("book")
        );
        [$remaining, $output, $err] = Combinator::recognize($parser)($input);
        self::assertEquals(Error::ERR_EOF, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}