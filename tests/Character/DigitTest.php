<?php

namespace Character;

use Parcom\Character;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Character::is_space
 * @covers \Parcom\Character::is_alphabetic
 * @covers \Parcom\Character::is_alphanumeric
 * @covers \Parcom\Character::is_digit
 * @covers \Parcom\Character::space0
 * @covers \Parcom\Character::space1
 * @covers \Parcom\Character::digit0
 * @covers \Parcom\Character::digit1
 * @covers \Parcom\Character::alpha0
 * @covers \Parcom\Character::alpha1
 * @covers \Parcom\Character::alphanumeric0
 * @covers \Parcom\Character::alphanumeric1
 * @covers \Parcom\Character::zeroOrMore
 * @covers \Parcom\Character::oneOrMore
 * @covers \Parcom\Character::minCountMatch
 */
class DigitTest extends TestCase
{

    public static function testIsSpace()
    {
        $valids = [" ", "\t"];
        foreach ($valids as $valid) {
            self::assertTrue(Character::is_space($valid));
        }
        $invalids = ["\n", "\r", 'a', 'z', '0', '9'];
        foreach ($invalids as $invalid) {
            self::assertFalse(Character::is_space($invalid));
        }
    }

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

    public function testAlpha0SuccessNoMatch()
    {
        $input = new Span("123");
        [$remaining, $output, $err] = Character::alpha0()($input);
        self::assertNull($err);
        self::assertEquals("", (string)$output);
        self::assertEquals("123", (string)$remaining);
    }

    public function testAlpha0SuccessMatch()
    {
        $input = new Span("aBc123");
        [$remaining, $output, $err] = Character::alpha0()($input);
        self::assertNull($err);
        self::assertEquals("aBc", (string)$output);
        self::assertEquals("123", (string)$remaining);
    }

    public function testAlpha1Success()
    {
        $input = new Span("aBc");
        [$remaining, $output, $err] = Character::alpha1()($input);
        self::assertNull($err);
        self::assertEquals("aBc", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testAlpha1SuccessWithRemainder()
    {
        $input = new Span("aBc123");
        [$remaining, $output, $err] = Character::alpha1()($input);
        self::assertNull($err);
        self::assertEquals("aBc", (string)$output);
        self::assertEquals("123", (string)$remaining);
    }

    public function testAlpha1Failure()
    {
        $input = new Span("123aBc");
        [$remaining, $output, $err] = Character::alpha1()($input);
        self::assertEquals(Error::ERR_ALPHABETIC, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testAlphanumeric0SuccessNoMatch()
    {
        $input = new Span("+[]=");
        [$remaining, $output, $err] = Character::alphanumeric0()($input);
        self::assertNull($err);
        self::assertEquals("", (string)$output);
        self::assertEquals("+[]=", (string)$remaining);
    }

    public function testAlphanumeric0SuccessMatch()
    {
        $input = new Span("aBc123[]");
        [$remaining, $output, $err] = Character::alphanumeric0()($input);
        self::assertNull($err);
        self::assertEquals("aBc123", (string)$output);
        self::assertEquals("[]", (string)$remaining);
    }

    public function testAlphanumeric1Success()
    {
        $input = new Span("aBc123");
        [$remaining, $output, $err] = Character::alphanumeric1()($input);
        self::assertNull($err);
        self::assertEquals("aBc123", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testAlphanumeric1SuccessWithRemainder()
    {
        $input = new Span("aBc123[]");
        [$remaining, $output, $err] = Character::alphanumeric1()($input);
        self::assertNull($err);
        self::assertEquals("aBc123", (string)$output);
        self::assertEquals("[]", (string)$remaining);
    }

    public function testAlphanumeric1Failure()
    {
        $input = new Span("+[]=");
        [$remaining, $output, $err] = Character::alphanumeric1()($input);
        self::assertEquals(Error::ERR_ALPHANUMERIC, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testSpace0SuccessNoMatch()
    {
        $input = new Span("");
        [$remaining, $output, $err] = Character::space0()($input);
        self::assertNull($err);
        self::assertEquals("", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testSpace0SuccessMatch()
    {
        $input = new Span(" \t");
        [$remaining, $output, $err] = Character::space0()($input);
        self::assertNull($err);
        self::assertEquals(" \t", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testSpace1Success()
    {
        $input = new Span(" \t");
        [$remaining, $output, $err] = Character::space1()($input);
        self::assertNull($err);
        self::assertEquals(" \t", (string)$output);
        self::assertEquals("", (string)$remaining);
    }

    public function testSpace1SuccessWithRemainder()
    {
        $input = new Span(" \ta+1");
        [$remaining, $output, $err] = Character::space1()($input);
        self::assertNull($err);
        self::assertEquals(" \t", (string)$output);
        self::assertEquals("a+1", (string)$remaining);
    }

    public function testSpace1Failure()
    {
        $input = new Span("a+1");
        [$remaining, $output, $err] = Character::space1()($input);
        self::assertEquals(Error::ERR_SPACE, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}