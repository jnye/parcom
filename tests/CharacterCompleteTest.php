<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Character\Complete\alpha0;
use function Parcom\Character\Complete\alpha1;
use function Parcom\Character\Complete\digit0;
use function Parcom\Character\Complete\digit1;

/**
 * @covers \Parcom\Character\is_alphabetic
 * @covers \Parcom\Character\is_digit
 * @covers \Parcom\Character\Complete\alpha0
 * @covers \Parcom\Character\Complete\alpha1
 * @covers \Parcom\Character\Complete\digit0
 * @covers \Parcom\Character\Complete\digit1
 */
class CharacterCompleteTest extends TestCase
{

    public function testAlpha0Success(): void
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testAlpha0SuccessRemaining(): void
    {
        $input = new Input("abc1def");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1def", $remaining);
    }

    public function testAlpha0SuccessZero(): void
    {
        $input = new Input("1def");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("1def", $remaining);
    }

    public function testAlpha0SuccessEof(): void
    {
        $input = new Input("");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("", $remaining);
    }

    public function testAlpha1Success(): void
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testAlpha1SuccessRemaining(): void
    {
        $input = new Input("abc1def");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1def", $remaining);
    }

    public function testAlpha1ErrorZero(): void
    {
        $input = new Input("1def");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertEquals(Err::Error($input, ErrorKind::Alpha()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testAlpha1ErrorEof(): void
    {
        $input = new Input("");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertEquals(Err::Error($input, ErrorKind::Alpha()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDigit0Success(): void
    {
        $input = new Input("123a");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a", $remaining);
    }

    public function testDigit0SuccessRemaining(): void
    {
        $input = new Input("123a456");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a456", $remaining);
    }

    public function testDigit0SuccessZero(): void
    {
        $input = new Input("a123");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("a123", $remaining);
    }

    public function testDigit0SuccessEof(): void
    {
        $input = new Input("");
        [$remaining, $output, $err] = digit0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("", $remaining);
    }

    public function testDigit1Success(): void
    {
        $input = new Input("123a");
        [$remaining, $output, $err] = digit1()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a", $remaining);
    }

    public function testDigit1SuccessRemaining(): void
    {
        $input = new Input("123a456");
        [$remaining, $output, $err] = digit1()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("a456", $remaining);
    }

    public function testDigit1ErrorZero(): void
    {
        $input = new Input("a123");
        [$remaining, $output, $err] = digit1()($input);
        self::assertEquals(Err::Error($input, ErrorKind::Digit()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDigit1ErrorEof(): void
    {
        $input = new Input("");
        [$remaining, $output, $err] = digit1()($input);
        self::assertEquals(Err::Error($input, ErrorKind::Digit()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}