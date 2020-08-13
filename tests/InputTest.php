<?php

use Parcom\Input;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Parcom\Input
 */
class InputTest extends TestCase
{

    public function testConstructionSimple()
    {
        $input = new Input("cat");
        self::assertNotNull($input);
        self::assertEquals("cat", $input);
        self::assertSame(3, $input->input_length());
    }

    public function testConstructionComplex()
    {
        $input = new Input("scatter", 1, 3);
        self::assertNotNull($input);
        self::assertEquals("cat", $input);
        self::assertSame(3, $input->input_length());
    }

    public function testTake()
    {
        $input = new Input("dog");
        $result = $input->take(2);
        self::assertInstanceOf(Input::class, $result);
        self::assertEquals("do", $result);
    }

    public function testTakeSplit()
    {
        $input = new Input("aircraft");
        $result = $input->take_split(3);
        self::assertIsArray($result);
        [$remaining, $output] = $result;
        self::assertInstanceOf(Input::class, $remaining);
        self::assertInstanceOf(Input::class, $output);
        self::assertEquals(["craft", "air"], [$remaining, $output]);
    }

}