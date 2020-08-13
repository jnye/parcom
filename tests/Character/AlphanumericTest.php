<?php

namespace Character;

use Parcom\Character;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Character::is_alphanumeric
 * @covers \Parcom\Character::alphanumeric0
 * @covers \Parcom\Character::alphanumeric1
 * @covers \Parcom\Character::zeroOrMore
 * @covers \Parcom\Character::oneOrMore
 * @covers \Parcom\Character::minCountMatch
 */
class AlphanumericTest extends TestCase
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

    public function testAlphanumeric0SuccessNoMatch()
    {
        $input = new Span("+[]=");
        [$remaining, $output, $err] = Character::alphanumeric0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("+[]=", $remaining);
    }

    public function testAlphanumeric0SuccessMatch()
    {
        $input = new Span("aBc123[]");
        [$remaining, $output, $err] = Character::alphanumeric0()($input);
        self::assertNull($err);
        self::assertEquals("aBc123", $output);
        self::assertEquals("[]", $remaining);
    }

    public function testAlphanumeric1Success()
    {
        $input = new Span("aBc123");
        [$remaining, $output, $err] = Character::alphanumeric1()($input);
        self::assertNull($err);
        self::assertEquals("aBc123", $output);
        self::assertEquals("", $remaining);
    }

    public function testAlphanumeric1SuccessWithRemainder()
    {
        $input = new Span("aBc123[]");
        [$remaining, $output, $err] = Character::alphanumeric1()($input);
        self::assertNull($err);
        self::assertEquals("aBc123", $output);
        self::assertEquals("[]", $remaining);
    }

    public function testAlphanumeric1Failure()
    {
        $input = new Span("+[]=");
        [$remaining, $output, $err] = Character::alphanumeric1()($input);
        self::assertEquals(Error::ERR_ALPHANUMERIC, $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}