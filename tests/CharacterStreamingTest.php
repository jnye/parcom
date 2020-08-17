<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\Needed;
use PHPUnit\Framework\TestCase;
use function Parcom\Character\Streaming\alpha0;
use function Parcom\Character\Streaming\alpha1;
use function Parcom\Character\Streaming\digit0;
use function Parcom\Character\Streaming\digit1;

/**
 * @covers \Parcom\Character\is_alphabetic
 * @covers \Parcom\Character\is_digit
 * @covers \Parcom\Character\Streaming\alpha0
 * @covers \Parcom\Character\Streaming\alpha1
 * @covers \Parcom\Character\Streaming\digit0
 * @covers \Parcom\Character\Streaming\digit1
 */
class CharacterStreamingTest extends TestCase
{

    public function testAlpha0Success()
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testAlpha0SuccessRemaining()
    {
        $input = new Input("abc1def");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1def", $remaining);
    }

    public function testAlpha0SuccessZero()
    {
        $input = new Input("1def");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("1def", $remaining);
    }

    public function testAlpha0Incomplete()
    {
        $input = new Input("");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testAlpha1Success()
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testAlpha1SuccessRemaining()
    {
        $input = new Input("abc1def");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1def", $remaining);
    }

    public function testAlpha1ErrorZero()
    {
        $input = new Input("1def");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertEquals(Err::Error($input, ErrorKind::Alpha()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testAlpha1Incomplete()
    {
        $input = new Input("");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDigit0Success()
    {
        $input = new Input("123a");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a", $remaining);
    }

    public function testDigit0SuccessRemaining()
    {
        $input = new Input("123a456");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a456", $remaining);
    }

    public function testDigit0SuccessZero()
    {
        $input = new Input("a123");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("a123", $remaining);
    }

    public function testDigit0Incomplete()
    {
        $input = new Input("");
        [$remaining, $output, $err] = digit0()($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDigit1Success()
    {
        $input = new Input("123a");
        [$remaining, $output, $err] = digit1()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a", $remaining);
    }

    public function testDigit1SuccessRemaining()
    {
        $input = new Input("123a456");
        [$remaining, $output, $err] = digit1()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a456", $remaining);
    }

    public function testDigit1ErrorZero()
    {
        $input = new Input("a123");
        [$remaining, $output, $err] = digit1()($input);
        self::assertEquals(Err::Error($input, ErrorKind::Digit()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDigit1Incomplete()
    {
        $input = new Input("");
        [$remaining, $output, $err] = digit1()($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}