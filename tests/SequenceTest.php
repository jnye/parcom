<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Bytes\Complete\tag;
use function Parcom\Sequence\delimited;
use function Parcom\Sequence\pair;
use function Parcom\Sequence\preceded;
use function Parcom\Sequence\separated_pair;
use function Parcom\Sequence\terminated;
use function Parcom\Sequence\tuple;

/**
 * @covers \Parcom\Sequence\delimited
 * @covers \Parcom\Sequence\pair
 * @covers \Parcom\Sequence\preceded
 * @covers \Parcom\Sequence\separated_pair
 * @covers \Parcom\Sequence\terminated
 * @covers \Parcom\Sequence\tuple
 */
class SequenceTest extends TestCase
{

    public function testDelimitedSuccess(): void
    {
        $input = new Input("abc");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("", $remaining);
    }

    public function testDelimitedSuccessRemainder(): void
    {
        $input = new Input("abcd");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("d", $remaining);
    }

    public function testDelimitedErrorFirst(): void
    {
        $input = new Input("Xbc");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDelimitedErrorMiddle(): void
    {
        $input = new Input("aXc");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("Xc"), ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testDelimitedErrorLast(): void
    {
        $input = new Input("abX");
        $parser = delimited(tag("a"), tag("b"), tag("c"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("X"), ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testPairSuccess(): void
    {
        $input = new Input("ab");
        $parser = pair(tag("a"), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(2, $outputs);
        self::assertEquals("a", $outputs[0]);
        self::assertEquals("b", $outputs[1]);
        self::assertEquals("", $remaining);
    }

    public function testPairSuccessRemainder(): void
    {
        $input = new Input("abc");
        $parser = pair(tag("a"), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(2, $outputs);
        self::assertEquals("a", $outputs[0]);
        self::assertEquals("b", $outputs[1]);
        self::assertEquals("c", $remaining);
    }

    public function testPairErrorFirst(): void
    {
        $input = new Input("Xb");
        $parser = pair(tag("a"), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testPairErrorSecond(): void
    {
        $input = new Input("aX");
        $parser = pair(tag("a"), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("X"), ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testPrecededSuccess(): void
    {
        $input = new Input("ab");
        $parser = preceded(tag("a"), tag("b"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("", $remaining);
    }

    public function testPrecededSuccessRemainder(): void
    {
        $input = new Input("abc");
        $parser = preceded(tag("a"), tag("b"));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("c", $remaining);
    }

    public function testPrecededErrorFirst(): void
    {
        $input = new Input("Xb");
        $parser = preceded(tag("a"), tag("b"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testPrecededErrorSecond(): void
    {
        $input = new Input("aX");
        $parser = preceded(tag("a"), tag("b"));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("X"), ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testSeparatedPairSuccess(): void
    {
        $input = new Input("a,b");
        $parser = separated_pair(tag("a"), tag(","), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(2, $outputs);
        self::assertEquals("a", $outputs[0]);
        self::assertEquals("b", $outputs[1]);
        self::assertEquals("", $remaining);
    }

    public function testSeparatedPairSuccessRemainder(): void
    {
        $input = new Input("a,bc");
        $parser = separated_pair(tag("a"), tag(","), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(2, $outputs);
        self::assertEquals("a", $outputs[0]);
        self::assertEquals("b", $outputs[1]);
        self::assertEquals("c", $remaining);
    }

    public function testSeparatedPairErrorFirst(): void
    {
        $input = new Input("X,b");
        $parser = separated_pair(tag("a"), tag(","), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testSeparatedPairErrorSep(): void
    {
        $input = new Input("aXb");
        $parser = separated_pair(tag("a"), tag(","), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("Xb"), ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testSeparatedPairErrorSecond(): void
    {
        $input = new Input("a,X");
        $parser = separated_pair(tag("a"), tag(","), tag("b"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("X"), ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testTerminatedSuccess(): void
    {
        $input = new Input("a,");
        $parser = terminated(tag("a"), tag(","));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("a", $output);
        self::assertEquals("", $remaining);
    }

    public function testTerminatedSuccessRemainder(): void
    {
        $input = new Input("a,b");
        $parser = terminated(tag("a"), tag(","));
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("a", $output);
        self::assertEquals("b", $remaining);
    }

    public function testTerminatedErrorFirst(): void
    {
        $input = new Input("X,");
        $parser = terminated(tag("a"), tag(","));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testTerminatedErrorSecond(): void
    {
        $input = new Input("aX");
        $parser = terminated(tag("a"), tag(","));
        [$remaining, $output, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("X"), ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testTupleSuccess(): void
    {
        $input = new Input("when");
        $parser = tuple(tag("w"), tag("h"), tag("e"), tag("n"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(4, $outputs);
        self::assertEquals("w", $outputs[0]);
        self::assertEquals("h", $outputs[1]);
        self::assertEquals("e", $outputs[2]);
        self::assertEquals("n", $outputs[3]);
        self::assertEquals("", $remaining);
    }

    public function testTupleSuccessRemaining(): void
    {
        $input = new Input("whence");
        $parser = tuple(tag("w"), tag("h"), tag("e"), tag("n"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(4, $outputs);
        self::assertEquals("w", $outputs[0]);
        self::assertEquals("h", $outputs[1]);
        self::assertEquals("e", $outputs[2]);
        self::assertEquals("n", $outputs[3]);
        self::assertEquals("ce", $remaining);
    }

    public function testTupleFailureFirst(): void
    {
        $input = new Input("when");
        $parser = tuple(tag("X"), tag("h"), tag("e"), tag("n"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testTupleFailureSecond(): void
    {
        $input = new Input("when");
        $parser = tuple(tag("w"), tag("X"), tag("e"), tag("n"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("hen"), ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testTupleFailureThird(): void
    {
        $input = new Input("when");
        $parser = tuple(tag("w"), tag("h"), tag("X"), tag("n"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("en"), ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testTupleFailureFourth(): void
    {
        $input = new Input("when");
        $parser = tuple(tag("w"), tag("h"), tag("e"), tag("X"));
        [$remaining, $outputs, $err] = $parser($input);
        self::assertEquals(Err::Error(new Input("n"), ErrorKind::Tag()), $err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

}
