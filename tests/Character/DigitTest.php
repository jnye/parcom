<?php

namespace Character;

use Parcom\Character;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Character::is_digit
 * @covers \Parcom\Character::digit0
 * @covers \Parcom\Character::digit1
 * @covers \Parcom\Character::zeroOrMore
 * @covers \Parcom\Character::oneOrMore
 * @covers \Parcom\Character::minCountMatch
 */
class DigitTest extends TestCase
{

    public static function testIsDigit()
    {
        $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        foreach ($digits as $digit) {
            self::assertTrue(Character::is_digit($digit));
        }
        $notDigits = ['a', 'A', '-', '+', '.'];
        foreach ($notDigits as $notDigit) {
            self::assertFalse(Character::is_digit($notDigit));
        }
    }

    public function testDigit0SuccessNoMatch()
    {
        $input = new Span("abc");
        [$remaining, $output, $err] = Character::digit0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("abc", $remaining);
    }

    public function testDigit0SuccessMatch()
    {
        $input = new Span("123abc");
        [$remaining, $output, $err] = Character::digit0()($input);
        self::assertNull($err);
        self::assertEquals("123", $output);
        self::assertEquals("abc", $remaining);
    }

    public function testDigit1Success()
    {
        $input = new Span("1234");
        [$remaining, $output, $err] = Character::digit1()($input);
        self::assertNull($err);
        self::assertEquals("1234", $output);
        self::assertEquals("", $remaining);
    }

    public function testDigit1SuccessWithRemainder()
    {
        $input = new Span("1234abc");
        [$remaining, $output, $err] = Character::digit1()($input);
        self::assertNull($err);
        self::assertEquals("1234", $output);
        self::assertEquals("abc", $remaining);
    }

    public function testDigit1Failure()
    {
        $input = new Span("abc123");
        [$remaining, $output, $err] = Character::digit1()($input);
        self::assertEquals(Error::ERR_DIGIT, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}