<?php

namespace Parcom\Benchmarks;

use Parcom\Error;
use Parcom\Span;

/**
 * @Revs(1000)
 * @Iterations(5)
 */
class SpanBench
{

    public function benchSpan()
    {
        new Span("aircraft");
    }

    /**
     * @throws Error
     */
    public function benchSpanFromSpan()
    {
        $input = new Span("aircraft");
        $input->span(4, 4);
    }

}