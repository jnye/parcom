<?php

namespace Sequence;


use Parcom\Bytes;
use Parcom\Error;
use Parcom\Sequence;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Sequence::terminated
 */
class TerminatedTest extends TestCase
{

    public function testTerminatedSuccess()
    {
        $input = new Span("aircraft;anvil");
        $parser = Sequence::terminated(Bytes::tag("aircraft"), Bytes::tag(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("aircraft", $output);
        self::assertEquals("anvil", $remaining);
    }

    public function testTerminatedFailureOnSecondParser()
    {
        $input = new Span("aircraftanvil");
        $parser = Sequence::terminated(Bytes::tag("aircraft"), Bytes::tag(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTerminatedFailureOnFirstParser()
    {
        $input = new Span("anvil;aardvark");
        $parser = Sequence::terminated(Bytes::tag("aircraft"), Bytes::tag(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Error::ERR_TAG, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}