<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Bytes\Complete\escaped;
use function Parcom\Bytes\Complete\is_a;
use function Parcom\Bytes\Complete\is_not;
use function Parcom\Bytes\Complete\tag;
use function Parcom\Bytes\Complete\tag_no_case;
use function Parcom\Bytes\Complete\take_till;
use function Parcom\Bytes\Complete\take_till1;
use function Parcom\Bytes\Complete\take_until;
use function Parcom\Bytes\Complete\take_while;
use function Parcom\Bytes\Complete\take_while1;
use function Parcom\Bytes\Complete\take_while_m_n;
use function Parcom\Character\Complete\alpha1;
use function Parcom\Character\Complete\digit1;
use function Parcom\Character\is_alphabetic;

/**
 * @covers \Parcom\Bytes\Complete\escaped
 * @covers \Parcom\Bytes\Complete\is_a
 * @covers \Parcom\Bytes\Complete\is_not
 * @covers \Parcom\Bytes\Complete\tag
 * @covers \Parcom\Bytes\Complete\tag_no_case
 * @covers \Parcom\Bytes\Complete\take_until
 * @covers \Parcom\Bytes\Complete\take_till
 * @covers \Parcom\Bytes\Complete\take_till1
 * @covers \Parcom\Bytes\Complete\take_while
 * @covers \Parcom\Bytes\Complete\take_while1
 * @covers \Parcom\Bytes\Complete\take_while_m_n
 */
class BytesCompleteTest extends TestCase
{

    public function testEscapedSuccess()
    {
        $input = new Input("a\\1;");
        $parser = escaped(alpha1(), '\\', digit1());
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("a\\1", $output);
        self::assertEquals(";", $remaining);
    }

    public function testEscapedSuccessEof()
    {
        $input = new Input("a\\1");
        $parser = escaped(alpha1(), '\\', digit1());
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("a\\1", $output);
        self::assertEquals("", $remaining);
    }

    public function testEscapedError()
    {
        $input = new Input("a\\b;");
        $parser = escaped(alpha1(), '\\', digit1());
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("b;"), ErrorKind::Digit()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testEscapedSuccessBlank()
    {
        $input = new Input("");
        $parser = escaped(alpha1(), '\\', digit1());
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("", $remaining);
    }

    public function testEscapedErrorNoControlChar()
    {
        $input = new Input("a");
        $parser = escaped(alpha1(), '\\', digit1());
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input(""), ErrorKind::Escaped()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testIsASuccessRemaining()
    {
        $input = new Input("example");
        $parser = is_a("aex");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("exa", $output);
        self::assertEquals("mple", $remaining);
    }

    public function testIsASuccessAll()
    {
        $input = new Input("exa");
        $parser = is_a("aex");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("exa", $output);
        self::assertEquals("", $remaining);
    }

    public function testIsAErrorNone()
    {
        $input = new Input("");
        $parser = is_a("aex");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::IsA()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testIsNotSuccessRemaining()
    {
        $input = new Input("example");
        $parser = is_not("lmp");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("exa", $output);
        self::assertEquals("mple", $remaining);
    }

    public function testIsNotSuccessAll()
    {
        $input = new Input("exa");
        $parser = is_not("ghi");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("exa", $output);
        self::assertEquals("", $remaining);
    }

    public function testIsNotErrorNone()
    {
        $input = new Input("");
        $parser = is_not("fop");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::IsNot()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTagSuccessWithRemainder()
    {
        $input = new Input("football");
        $parser = tag("foot");
        [$remaining, $output, $err] = $parser($input);
        self::assertNotNull($output);
        self::assertEquals("ball", $remaining);
        self::assertNotNull($remaining);
        self::assertEquals("foot", $output);
        self::assertNull($err);
    }

    public function testTagSuccessWithNoRemainder()
    {
        $input = new Input("football");
        $parser = tag("football");
        [$remaining, $output, $err] = $parser($input);
        self::assertNotNull($remaining);
        self::assertEquals("", $remaining);
        self::assertNotNull($output);
        self::assertEquals("football", $output);
        self::assertNull($err);
    }

    public function testTagIncomplete()
    {
        $input = new Input("foot");
        $parser = tag("football");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($output);
        self::assertNull($remaining);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
    }

    public function testTagError()
    {
        $input = new Input("football");
        $parser = tag("book");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($output);
        self::assertNull($remaining);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
    }

    public function testTagNoCaseSuccessWithRemainder()
    {
        $input = new Input("football");
        $parser = tag_no_case("FOOT");
        [$left, $right, $err] = $parser($input);
        self::assertNotNull($left);
        self::assertEquals("ball", $left);
        self::assertNotNull($right);
        self::assertEquals("foot", $right);
        self::assertNull($err);
    }

    public function testTagNoCaseSuccessWithNoRemainder()
    {
        $input = new Input("football");
        $parser = tag_no_case("FOOTBALL");
        [$left, $right, $err] = $parser($input);
        self::assertNotNull($left);
        self::assertEquals("", $left);
        self::assertNotNull($right);
        self::assertEquals("football", $right);
        self::assertNull($err);
    }

    public function testTagNoCaseErrorPartial()
    {
        $input = new Input("foot");
        $parser = tag_no_case("FOOTBALL");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
    }

    public function testTagNoCaseError()
    {
        $input = new Input("football");
        $parser = tag_no_case("BOOK");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
    }

    public function testTakeUntilSuccessAll()
    {
        $input = new Input("breaker;");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";", $remaining);
    }

    public function testTakeUntilSuccessRemaining()
    {
        $input = new Input("breaker;ship");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";ship", $remaining);
    }

    public function testTakeUntilSuccessNothing()
    {
        $input = new Input(";break");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals(";break", $remaining);
    }

    public function testTakeUntilErrorNoEnd()
    {
        $input = new Input("break");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeUntil()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeUntilErrorNone()
    {
        $input = new Input("");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeUntil()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeTillSuccessAll()
    {
        $input = new Input("breaker;");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";", $remaining);
    }

    public function testTakeTillSuccessRemaining()
    {
        $input = new Input("breaker;ship");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";ship", $remaining);
    }

    public function testTakeTillSuccessNothing()
    {
        $input = new Input(";break");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals(";break", $remaining);
    }

    public function testTakeTill1SuccessAll()
    {
        $input = new Input("breaker;");
        $parser = take_till1(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";", $remaining);
    }

    public function testTakeTill1SuccessRemaining()
    {
        $input = new Input("breaker;ship");
        $parser = take_till1(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("breaker", $output);
        self::assertEquals(";ship", $remaining);
    }

    public function testTakeTill1Error()
    {
        $input = new Input(";break");
        $parser = take_till1(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeTill1()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeTill1Eof()
    {
        $input = new Input("break");
        $parser = take_till1(fn($c) => $c == ';');
        [$output, $remaining, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("break", $remaining);
    }

    public function testTakeWhileSuccessRemaining()
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = take_while(fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testTakeWhileSuccessAll()
    {
        $input = new Input("abc");
        [$remaining, $output, $err] = take_while(fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("", $remaining);
    }

    public function testTakeWhileSuccessNone()
    {
        $input = new Input("");
        [$remaining, $output, $err] = take_while(fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("", $output);
        self::assertEquals("", $remaining);
    }

    public function testTakeWhile1SuccessRemaining()
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = take_while1(fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testTakeWhile1Error()
    {
        $input = new Input("123");
        [$remaining, $output, $err] = take_while1(fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeWhile1()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhile1SuccessAll()
    {
        $input = new Input("abc");
        [$remaining, $output, $err] = take_while1(fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("", $remaining);
    }

    public function testTakeWhile1Eof()
    {
        $input = new Input("");
        [$remaining, $output, $err] = take_while1(fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeWhile1()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNSuccessInBetweenMinMaxRemaining()
    {
        $input = new Input("peach123");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("peach", $output);
        self::assertEquals("123", $remaining);
    }

    public function testTakeWhileMNSuccessMaxWithRemaining()
    {
        $input = new Input("squeaky");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("squeak", $output);
        self::assertEquals("y", $remaining);
    }

    public function testTakeWhileMNSuccessMaxWithRemaining2()
    {
        $input = new Input("buttons1");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("button", $output);
        self::assertEquals("s1", $remaining);
    }

    public function testTakeWhileMNSuccessInBetweenMinMaxEof()
    {
        $input = new Input("peach");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("peach", $output);
        self::assertEquals("", $remaining);
    }

    public function testTakeWhileMNErrorEof()
    {
        $input = new Input("");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeWhileMN()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNErrorUnderMin()
    {
        $input = new Input("go");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeWhileMN()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNErrorNone()
    {
        $input = new Input("12345");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeWhileMN()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}
