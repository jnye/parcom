<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Branch\alt;
use function Parcom\Branch\permutation;
use function Parcom\Bytes\Complete\tag;

/**
 * @covers \Parcom\Branch\alt
 * @covers \Parcom\Branch\permutation
 */
class BranchTest extends TestCase
{

    public function testAltSuccessFirst(): void
    {
        $input = new Input("a");
        $parser = alt(
            tag("a"),
            tag("b")
        );
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("a", $output);
        self::assertEquals("", $remaining);
    }

    public function testAltSuccessSecond(): void
    {
        $input = new Input("b");
        $parser = alt(
            tag("a"),
            tag("b")
        );
        [$remaining, $output, $err] = $parser($input);
        self::assertNull($err);
        self::assertEquals("b", $output);
        self::assertEquals("", $remaining);
    }

    public function testAltError(): void
    {
        $input = new Input("c");
        $parser = alt(
            tag("a"),
            tag("b")
        );
        [$remaining, $output, $err] = $parser($input);
        self::assertNotNull($err);
        self::assertEquals(Err::Error($input, ErrorKind::Tag()), $err);
        self::assertNull($output);
        self::assertNull($remaining);
    }

    public function testPermutationSuccessInOrder(): void
    {
        $input = new Input("abc123-+=");
        $parser = permutation(
            tag("abc"),
            tag("123"),
            tag("-+=")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(3, $outputs);
        self::assertEquals("abc", $outputs[0]);
        self::assertEquals("123", $outputs[1]);
        self::assertEquals("-+=", $outputs[2]);
        self::assertEquals("", $remaining);
    }

    public function testPermutationSuccessOutOfOrder(): void
    {
        $input = new Input("-+=abc123");
        $parser = permutation(
            tag("123"),
            tag("abc"),
            tag("-+=")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNull($err);
        self::assertIsArray($outputs);
        self::assertCount(3, $outputs);
        self::assertEquals("123", $outputs[0]);
        self::assertEquals("abc", $outputs[1]);
        self::assertEquals("-+=", $outputs[2]);
        self::assertEquals("", $remaining);
    }

    public function testPermutationErrorNoMatches(): void
    {
        $input = new Input("foobar");
        $parser = permutation(
            tag("123"),
            tag("abc"),
            tag("-+=")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNotNull($err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

    public function testPermutationErrorSomeMatches(): void
    {
        $input = new Input("123foo");
        $parser = permutation(
            tag("123"),
            tag("abc"),
            tag("-+=")
        );
        [$remaining, $outputs, $err] = $parser($input);
        self::assertNotNull($err);
        self::assertNull($outputs);
        self::assertNull($remaining);
    }

}