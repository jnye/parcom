<?php

use Parcom\Err;
use Parcom\ErrorKind;
use Parcom\Input;
use PHPUnit\Framework\TestCase;
use function Parcom\Branch\alt;
use function Parcom\Bytes\Complete\tag;

/**
 * @covers \Parcom\Branch\alt
 */
class BranchTest extends TestCase
{

    public function testAltSuccessFirst()
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

    public function testAltSuccessSecond()
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

    public function testAltError()
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

}