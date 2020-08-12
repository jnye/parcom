<?php

namespace Character;

use Parcom\Character;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Character::is_alphabetic
 * @covers \Parcom\Character::is_alphanumeric
 * @covers \Parcom\Character::is_digit
 * @covers \Parcom\Character::digit0
 * @covers \Parcom\Character::digit1
 */
class DigitTest extends TestCase
{

    public static function testIsAlphanumeric()
    {
        $valids = ['a', 'A', 'z', 'Z', 'm', 'M', '0', '9', '5'];
        foreach ($valids as $valid) {
            self::assertTrue(Character::is_alphanumeric($valid));
        }
        $invalids = ['-', '+', '.'];
        foreach ($invalids as $invalid) {
            self::assertFalse(Character::is_alphanumeric($invalid));
        }
    }

    public static function testIsAlphabetic()
    {
        $letters = ['a', 'A', 'z', 'Z', 'm', 'M'];
        foreach ($letters as $letter) {
            self::assertTrue(Character::is_alphabetic($letter));
        }
        $notLetters = ['-', '+', '.', '4'];
        foreach ($notLetters as $notLetter) {
            self::assertFalse(Character::is_alphabetic($notLetter));
        }
    }

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
        self::assertEquals("", (string)$output);
        self::assertEquals("abc", (string)$remaining);
    }

    public function testDigit0SuccessMatch()
    {
        $input = new Span("123abc");
        [$remaining, $output, $err] = Character::digit0()($input);
        self::assertNull($err);
        self::assertEquals("123", (string)$output);
        self::assertEquals("abc", (string)$remaining);
    }

    public function testDigit1Success()
    {
        $input = new Span("1234");
        [$remaining, $output, $err] = Character::digit1()($input);
        self::assertNull($err);
        self::assertEquals("1234", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testDigit1SuccessWithRemainder()
    {
        $input = new Span("1234abc");
        [$remaining, $output, $err] = Character::digit1()($input);
        self::assertNull($err);
        self::assertEquals("1234", (string)$output);
        self::assertEquals("abc", (string)$remaining);
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