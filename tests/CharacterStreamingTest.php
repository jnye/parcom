<?php

use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Character\Streaming\alpha0;
use function Parcom\Character\Streaming\alpha1;

/**
 * @covers \Parcom\Character\is_alphabetic
 * @covers \Parcom\Character\Streaming\alpha0
 * @covers \Parcom\Character\Streaming\alpha1
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
        self::assertEquals(\Parcom\Err::Incomplete(\Parcom\Needed::Size(1)), $err);
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
        self::assertEquals(\Parcom\Err::Error($input, \Parcom\ErrorKind::Alpha()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testAlpha1Incomplete()
    {
        $input = new Input("");
        [$remaining, $output, $err] = alpha1()($input);
        self::assertEquals(\Parcom\Err::Incomplete(\Parcom\Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}