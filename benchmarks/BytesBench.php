<?php

use Parcom\Bytes;
use Parcom\Span;

/**
 * @Revs(1000)
 * @Iterations(5)
 */
class BytesBench
{

    public function benchTake()
    {
        $input = new Span("aircraft");
        $parser = Bytes::take(3);
        $parser($input);
    }

    public function benchTag()
    {
        $input = new Span("aircraft");
        $parser = Bytes::tag("air");
        $parser($input);
    }

}