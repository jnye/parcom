<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\Needed;
use PHPUnit\Framework\TestCase;
use function Parcom\Bytes\Streaming\tag;
use function Parcom\Bytes\Streaming\take_till;
use function Parcom\Bytes\Streaming\take_till1;

/**
 * @covers \Parcom\Bytes\Streaming\tag
 * @covers \Parcom\Bytes\Streaming\take_till
 * @covers \Parcom\Bytes\Streaming\take_till1
 */
class BytesStreamingTest extends TestCase
{

    public function testTagSuccessWithRemainder()
    {
        $input = new Input("football");
        $parser = tag("foot");
        [$left, $right, $err] = $parser($input);
        self::assertNotNull($left);
        self::assertEquals("ball", $left);
        self::assertNotNull($right);
        self::assertEquals("foot", $right);
        self::assertNull($err);
    }

    public function testTagSuccessWithNoRemainder()
    {
        $input = new Input("football");
        $parser = tag("football");
        [$left, $right, $err] = $parser($input);
        self::assertNotNull($left);
        self::assertEquals("", $left);
        self::assertNotNull($right);
        self::assertEquals("football", $right);
        self::assertNull($err);
    }

    public function testTagIncomplete()
    {
        $input = new Input("foot");
        $parser = tag("football");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
        self::assertNotNull($err);
        self::assertEquals(Err::Incomplete(Needed::Size(4)), $err);
    }

    public function testTagError()
    {
        $input = new Input("football");
        $parser = tag("book");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
    }

    public function testTakeTillSuccessAll()
    {
        $input = new Input("breaker;");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";", $remaining);
    }

    public function testTakeTillSuccessRemaining()
    {
        $input = new Input("breaker;ship");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";ship", $remaining);
    }

    public function testTakeTillSuccessNothing()
    {
        $input = new Input(";break");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals(";break", $remaining);
    }

    public function testTakeTillIncomplete()
    {
        $input = new Input("break");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), (string)$err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeTill1SuccessAll()
    {
        $input = new Input("breaker;");
        $parser = take_till1(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";", $remaining);
    }

    public function testTakeTill1SuccessRemaining()
    {
        $input = new Input("breaker;ship");
        $parser = take_till1(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";ship", $remaining);
    }

    public function testTakeTill1Error()
    {
        $input = new Input(";break");
        $parser = take_till1(fn($c) => $c == ';');
        [$left, $right, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeTill1()), $err);
        self::assertNull($left);
        self::assertNull($right);
    }

    public function testTakeTill1Incomplete()
    {
        $input = new Input("break");
        $parser = take_till1(fn($c) => $c == ';');
        [$left, $right, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), (string)$err);
        self::assertNull($left);
        self::assertNull($right);
    }

}