<?php

namespace Branch;

use Parcom\Branch;
use Parcom\Bytes;
use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Branch::permutation
 */
class PermutationTest extends TestCase
{

    public function testPermutationSimple()
    {
        $span = new Span("ab1");
        [$remainder, $output, $err] = Branch::permutation([Bytes::tag("a")])($span);
        self::assertEquals(null, $err);
        self::assertCount(1, $output);
        self::assertEquals("a", (string)$output[0]);
        self::assertEquals("b1", (string)$remainder);
    }

    public function testPermutationSimpleTwo()
    {
        $span = new Span("ab1");
        [$remainder, $output, $err] = Branch::permutation([
            Bytes::tag("a"),
            Bytes::tag("b1")
        ])($span);
        self::assertEquals(null, $err);
        self::assertCount(2, $output);
        self::assertEquals("a", (string)$output[0]);
        self::assertEquals("b1", (string)$output[1]);
        self::assertEquals("", (string)$remainder);
    }

    public function testPermutationSimpleTwoReverse()
    {
        $span = new Span("ab1");
        [$remainder, $output, $err] = Branch::permutation([
            Bytes::tag("b1"),
            Bytes::tag("a")
        ])($span);
        self::assertEquals(null, $err);
        self::assertCount(2, $output);
        self::assertEquals("a", (string)$output[0]);
        self::assertEquals("b1", (string)$output[1]);
        self::assertEquals("", (string)$remainder);
    }

    public function testPermutationSimpleThreeComplex()
    {
        $span = new Span("abc123ABC");
        [$remainder, $output, $err] = Branch::permutation([
            Bytes::tag("123"),
            Bytes::tag("ABC"),
            Bytes::tag("abc")
        ])($span);
        self::assertEquals(null, $err);
        self::assertCount(3, $output);
        self::assertEquals("abc", (string)$output[0]);
        self::assertEquals("123", (string)$output[1]);
        self::assertEquals("ABC", (string)$output[2]);
        self::assertEquals("", (string)$remainder);
    }

    public function testPermutationSimpleThreeComplexFailure()
    {
        $span = new Span("abcABC");
        $parser = Branch::permutation([
            Bytes::tag("123"),
            Bytes::tag("ABC"),
            Bytes::tag("abc")
        ]);
        [$remainder, $output, $err] = $parser($span);
        self::assertEquals(Error::ERR_PERMUTATION, $err);
        self::assertNull($remainder);
        self::assertNull($output);
    }

}