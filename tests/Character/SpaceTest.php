<?php

namespace Character;

use Parcom\Character;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Character::is_space
 * @covers \Parcom\Character::space0
 * @covers \Parcom\Character::space1
 * @covers \Parcom\Character::zeroOrMore
 * @covers \Parcom\Character::oneOrMore
 * @covers \Parcom\Character::minCountMatch
 */
class SpaceTest extends TestCase
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

    public function testSpace0SuccessNoMatch()
    {
        $input = new Span("");
        [$remaining, $output, $err] = Character::space0()($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("", $remaining);
    }

    public function testSpace0SuccessMatch()
    {
        $input = new Span(" \t");
        [$remaining, $output, $err] = Character::space0()($input);
        self::assertNull($err);
        self::assertEquals(" \t", $output);
        self::assertEquals("", $remaining);
    }

    public function testSpace1Success()
    {
        $input = new Span(" \t");
        [$remaining, $output, $err] = Character::space1()($input);
        self::assertNull($err);
        self::assertEquals(" \t", $output);
        self::assertEquals("", $remaining);
    }

    public function testSpace1SuccessWithRemainder()
    {
        $input = new Span(" \ta+1");
        [$remaining, $output, $err] = Character::space1()($input);
        self::assertNull($err);
        self::assertEquals(" \t", $output);
        self::assertEquals("a+1", $remaining);
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