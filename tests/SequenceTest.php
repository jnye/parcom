<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Bytes\Complete\tag;
use function Parcom\Sequence\delimited;

/**
 * @covers \Parcom\Sequence\delimited
 */
class SequenceTest extends TestCase
{

    public function testDelimitedSuccess()
    {
        $input = new Input("abc");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("", $remaining);
    }

    public function testDelimitedSuccessRemainder()
    {
        $input = new Input("abcd");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("d", $remaining);
    }

    public function testDelimitedErrorFirst()
    {
        $input = new Input("Xbc");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDelimitedErrorMiddle()
    {
        $input = new Input("aXc");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("Xc"), ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDelimitedErrorLast()
    {
        $input = new Input("abX");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("X"), ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}