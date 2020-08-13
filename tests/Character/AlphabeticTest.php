<?php

namespace Character;

use Parcom\Character;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Character::is_alphabetic
 * @covers \Parcom\Character::alpha0
 * @covers \Parcom\Character::alpha1
 * @covers \Parcom\Character::zeroOrMore
 * @covers \Parcom\Character::oneOrMore
 * @covers \Parcom\Character::minCountMatch
 */
class AlphabeticTest extends TestCase
{

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

    public function testAlpha0SuccessNoMatch()
    {
        $input = new Span("123");
        [$remaining, $output, $err] = Character::alpha0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("123", $remaining);
    }

    public function testAlpha0SuccessMatch()
    {
        $input = new Span("aBc123");
        [$remaining, $output, $err] = Character::alpha0()($input);
        self::assertNull($err);
        self::assertEquals("aBc", $output);
        self::assertEquals("123", $remaining);
    }

    public function testAlpha1Success()
    {
        $input = new Span("aBc");
        [$remaining, $output, $err] = Character::alpha1()($input);
        self::assertNull($err);
        self::assertEquals("aBc", $output);
        self::assertEquals("", $remaining);
    }

    public function testAlpha1SuccessWithRemainder()
    {
        $input = new Span("aBc123");
        [$remaining, $output, $err] = Character::alpha1()($input);
        self::assertNull($err);
        self::assertEquals("aBc", $output);
        self::assertEquals("123", $remaining);
    }

    public function testAlpha1Failure()
    {
        $input = new Span("123aBc");
        [$remaining, $output, $err] = Character::alpha1()($input);
        self::assertEquals(Error::ERR_ALPHABETIC, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}