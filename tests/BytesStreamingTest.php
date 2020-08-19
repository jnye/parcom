<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use Parcom\Needed;
use PHPUnit\Framework\TestCase;
use function Parcom\Bytes\Streaming\is_a;
use function Parcom\Bytes\Streaming\is_not;
use function Parcom\Bytes\Streaming\tag;
use function Parcom\Bytes\Streaming\tag_no_case;
use function Parcom\Bytes\Streaming\take_till;
use function Parcom\Bytes\Streaming\take_till1;
use function Parcom\Bytes\Streaming\take_until;
use function Parcom\Bytes\Streaming\take_while;
use function Parcom\Bytes\Streaming\take_while1;
use function Parcom\Bytes\Streaming\take_while_m_n;
use function Parcom\Character\is_alphabetic;

/**
 * @covers \Parcom\Bytes\Streaming\is_a
 * @covers \Parcom\Bytes\Streaming\is_not
 * @covers \Parcom\Bytes\Streaming\tag
 * @covers \Parcom\Bytes\Streaming\tag_no_case
 * @covers \Parcom\Bytes\Streaming\take_until
 * @covers \Parcom\Bytes\Streaming\take_till
 * @covers \Parcom\Bytes\Streaming\take_till1
 * @covers \Parcom\Bytes\Streaming\take_while
 * @covers \Parcom\Bytes\Streaming\take_while1
 * @covers \Parcom\Bytes\Streaming\take_while_m_n
 */
class BytesStreamingTest extends TestCase
{

    public function testIsASuccess()
    {
        $input = new Input("example");
        $parser = is_a("aex");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("exa", $output);
        self::assertEquals("mple", $remaining);
    }

    public function testIsAIncompleteSome()
    {
        $input = new Input("exa");
        $parser = is_a("aex");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testIsAIncompleteNone()
    {
        $input = new Input("");
        $parser = is_a("aex");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testIsNotSuccess()
    {
        $input = new Input("example");
        $parser = is_not("lmp");
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("exa", $output);
        self::assertEquals("mple", $remaining);
    }

    public function testIsNotIncompleteSome()
    {
        $input = new Input("exa");
        $parser = is_not("ghi");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testIsNotIncompleteNone()
    {
        $input = new Input("");
        $parser = is_not("fop");
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTagSuccessWithRemainder()
    {
        $input = new Input("football");
        $parser = tag("foot");
        [$left, $right, $err] = $parser($input);
        self::assertNotNull($left);
        self::assertEquals("ball", $left);
        self::assertNotNull($right);
        self::assertEquals("foot", $right);
        self::assertNull($err);
    }

    public function testTagSuccessWithNoRemainder()
    {
        $input = new Input("football");
        $parser = tag("football");
        [$left, $right, $err] = $parser($input);
        self::assertNotNull($left);
        self::assertEquals("", $left);
        self::assertNotNull($right);
        self::assertEquals("football", $right);
        self::assertNull($err);
    }

    public function testTagIncomplete()
    {
        $input = new Input("foot");
        $parser = tag("football");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
        self::assertNotNull($err);
        self::assertEquals(Err::Incomplete(Needed::Size(4)), $err);
    }

    public function testTagError()
    {
        $input = new Input("football");
        $parser = tag("book");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
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

    public function testTagNoCaseIncomplete()
    {
        $input = new Input("foot");
        $parser = tag_no_case("FOOTBALL");
        [$left, $right, $err] = $parser($input);
        self::assertNull($left);
        self::assertNull($right);
        self::assertNotNull($err);
        self::assertEquals(Err::Incomplete(Needed::Size(4)), $err);
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

    public function testTakeUntilIncompletePartial()
    {
        $input = new Input("break");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), (string)$err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeUntilIncompleteNone()
    {
        $input = new Input("");
        $parser = take_until(new Input(";"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), (string)$err);
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

    public function testTakeTillIncomplete()
    {
        $input = new Input("break");
        $parser = take_till(fn($c) => $c == ';');
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), (string)$err);
        self::assertNull($output);
        self::assertNull($remaining);
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
        [$left, $right, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeTill1()), $err);
        self::assertNull($left);
        self::assertNull($right);
    }

    public function testTakeTill1Incomplete()
    {
        $input = new Input("break");
        $parser = take_till1(fn($c) => $c == ';');
        [$left, $right, $err] = $parser($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), (string)$err);
        self::assertNull($left);
        self::assertNull($right);
    }

    public function testTakeWhileSuccess()
    {
        $input = new Input("abc1");
        [$remaining, $output, $err] = take_while(fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("abc", $output);
        self::assertEquals("1", $remaining);
    }

    public function testTakeWhileIncomplete()
    {
        $input = new Input("abc");
        [$remaining, $output, $err] = take_while(fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileEof()
    {
        $input = new Input("");
        [$remaining, $output, $err] = take_while(fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhile1Success()
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

    public function testTakeWhile1Incomplete()
    {
        $input = new Input("abc");
        [$remaining, $output, $err] = take_while1(fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhile1Eof()
    {
        $input = new Input("");
        [$remaining, $output, $err] = take_while1(fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNSuccess()
    {
        $input = new Input("peach123");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("peach", $output);
        self::assertEquals("123", $remaining);
    }

    public function testTakeWhileMNSuccessMaxWithEof()
    {
        $input = new Input("squeaky");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("squeak", $output);
        self::assertEquals("y", $remaining);
    }

    public function testTakeWhileMNSuccessMaxWithCondition()
    {
        $input = new Input("buttons1");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertNull($err);
        self::assertEquals("button", $output);
        self::assertEquals("s1", $remaining);
    }

    public function testTakeWhileMNIncompleteMax()
    {
        $input = new Input("peach");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNIncompleteEmptyMin()
    {
        $input = new Input("");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(3)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNIncompletePartialMin()
    {
        $input = new Input("go");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Incomplete(Needed::Size(1)), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTakeWhileMNError()
    {
        $input = new Input("12345");
        [$remaining, $output, $err] = take_while_m_n(3, 6, fn($c) => is_alphabetic($c))($input);
        self::assertEquals(Err::Error($input, ErrorKind::TakeWhileMN()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

}
