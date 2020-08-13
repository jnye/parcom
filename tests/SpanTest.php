<?php

use Parcom\Error;
use Parcom\Span;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Span
 */
class SpanTest extends TestCase
{

    public function testSliceToString()
    {
        $slice = new Span("aircraft");
        self::assertEquals("aircraft", $slice);
        self::assertSame(0, $slice->offset());
        self::assertSame(8, $slice->length());
        self::assertSame("aircraft", $slice->input());
    }

    public function testSliceArrayOffsetExists()
    {
        $slice = new Span("abc");
        self::assertFalse(isset($slice[-1]));
        self::assertTrue(isset($slice[0]));
        self::assertTrue(isset($slice[1]));
        self::assertTrue(isset($slice[2]));
        self::assertFalse(isset($slice[3]));
    }

    public function testSliceArrayOffsetGetInvalidIndexesUnder(){
        $slice = new Span("abc");
        $this->expectException(Error::class);
        $slice[-1];
    }

    public function testSliceArrayOffsetGetInvalidIndexesOver(){
        $slice = new Span("abc");
        $this->expectException(Error::class);
        $slice[3];
    }

    public function testSliceArrayOffsetGetValidIndexes()
    {
        $slice = new Span("abc");
        self::assertEquals("a", $slice[0]);
        self::assertEquals("b", $slice[1]);
        self::assertEquals("c", $slice[2]);
    }

    public function testSliceArrayOffsetSetNotSupported()
    {
        $slice = new Span("abc");
        $this->expectException(Error::class);
        $slice[0] = 'A';
    }

    public function testSliceArrayOffsetUnsetNotSupported()
    {
        $slice = new Span("abc");
        $this->expectException(Error::class);
        unset($slice[0]);
    }

    public function testSliceLen() {
        $slice = new Span("");
        self::assertEquals(0, $slice->length());
        $slice = new Span("abc");
        self::assertEquals(3, $slice->length());
    }

    /**
     * @throws Error
     */
    public function testSliceOfSlice()
    {
        $slice = new Span("aircraft");
        self::assertEquals("irc", $slice->span(1, 3));
    }

    public function testSpanBadLength()
    {
        $this->expectException(Error::class);
        new Span("abc", 0, 4);
    }

    public function testSpanOfSpanBadLength()
    {
        $this->expectException(Error::class);
        $span = new Span("abc");
        $span->span(0, 4);
    }

}