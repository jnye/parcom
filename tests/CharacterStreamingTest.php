<?php

use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Character\Streaming\alpha0;

/**
 * @covers \Parcom\Character\is_alphabetic
 * @covers \Parcom\Character\Streaming\alpha0
 */
class CharacterStreamingTest extends TestCase
{

    public function testAlpha0Success()
    {
        $input = new Input("abc");
        [$remaining, $output, $err] = alpha0()($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("", $remaining);
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
        self::assertEquals(\Parcom\Err::Incomplete(\Parcom\Needed::Unknown()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}