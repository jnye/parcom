<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Bytes\Complete\tag;
use function Parcom\Bytes\Complete\take_till;
use function Parcom\Bytes\Complete\take_till1;

/**
 * @covers \Parcom\Bytes\Complete\tag
 * @covers \Parcom\Bytes\Complete\take_till
 * @covers \Parcom\Bytes\Complete\take_till1
 */
class BytesCompleteTest extends TestCase
{

    public function testTagSuccessWithRemainder()
    {
        $input = new Input("football");
        $parser = tag("foot");
        [$remaining, $output, $err] = $parser($input);
        self::assertNotNull($output);
        self::assertEquals("ball", $remaining);
        self::assertNotNull($remaining);
        self::assertEquals("foot", $output);
        self::assertNull($err);
    }

    public function testTagSuccessWithNoRemainder()
    {
        $input = new Input("football");
        $parser = tag("football");
        [$remaining, $output, $err] = $parser($input);
        self::assertNotNull($remaining);
        self::assertEquals("", $remaining);
        self::assertNotNull($output);
        self::assertEquals("football", $output);
        self::assertNull($err);
    }

    public function testTagIncomplete()
    {
        $input = new Input("foot");
        $parser = tag("football");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($output);
        self::assertNull($remaining);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
    }

    public function testTagError()
    {
        $input = new Input("football");
        $parser = tag("book");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($output);
        self::assertNull($remaining);
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
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeTill1()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeTill1Eof()
    {
        $input = new Input("break");
        $parser = take_till1(fn($c) => $c == ';');
        [$output, $remaining, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("break", $remaining);
    }

}