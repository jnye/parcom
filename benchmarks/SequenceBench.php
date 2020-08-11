<?php

namespace Parcom\Benchmarks;

use Parcom\Bytes;
use Parcom\Sequence;
use Parcom\Span;

/**
 * @Revs(1000)
 * @Iterations(5)
 */
class SequenceBench
{

    public function benchPreceded()
    {
        $input = new Span('$abc');
        $parser = Sequence::preceded(Bytes::tag("$"), Bytes::tag("abc"));
        $parser($input);
    }

    public function benchPair()
    {
        $input = new Span('$abc');
        $parser = Sequence::pair(Bytes::tag("$"), Bytes::tag("abc"));
        $parser($input);
    }

}