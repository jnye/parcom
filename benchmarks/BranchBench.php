<?php

use Parcom\Branch;
use Parcom\Bytes;
use Parcom\Span;

/**
 * @Revs(1000)
 * @Iterations(5)
 */
class BranchBench
{

    public function benchChoiceInOrder()
    {
        $input = new Span('$abc');
        $parser = Branch::choice([Bytes::tag('$'), Bytes::tag("abc")]);
        $parser($input);
    }

    public function benchChoiceOutOfOrder()
    {
        $input = new Span('$abc');
        $parser = Branch::choice([Bytes::tag("abc"), Bytes::tag('$')]);
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