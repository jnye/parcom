<?php

namespace Parcom\Benchmarks;

use Parcom\Branch;
use Parcom\Bytes;
use Parcom\Span;

/**
 * @Revs(1000)
 * @Iterations(5)
 */
class BranchBench
{

    public function benchAltInOrder()
    {
        $input = new Span('$abc');
        $parser = Branch::alt([Bytes::tag('$'), Bytes::tag("abc")]);
        $parser($input);
    }

    public function benchAltOutOfOrder()
    {
        $input = new Span('$abc');
        $parser = Branch::alt([Bytes::tag("abc"), Bytes::tag('$')]);
        $parser($input);
    }

    public function benchPermutationInOrder()
    {
        $input = new Span('$abc');
        $parser = Branch::permutation([Bytes::tag('$'), Bytes::tag("abc")]);
        $parser($input);
    }

    public function benchPermutationOutOfOrder()
    {
        $input = new Span('$aircraft');
        $parser = Branch::permutation([Bytes::tag("air"), Bytes::tag('$'), Bytes::tag("craft")]);
        $parser($input);
    }

}