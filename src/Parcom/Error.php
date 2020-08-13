<?php

namespace Parcom;

use Exception;

class Error extends Exception
{

    const ERR_EOF = "Err::Eof";
    const ERR_PERMUTATION = "Err::Permutation";
    const ERR_TAG = "Err::Tag";
    const ERR_DIGIT = "Err::Digit";
    const ERR_ALPHABETIC = "Err::Alphabetic";
    const ERR_ALPHANUMERIC = "Err::Alphanumeric";
    const ERR_SPACE = "Err::Space";
    const ERR_CRLF = "Err::Crlf";
    const ERR_LF = "Err::Lf";
    const ERR_LINE_ENDING = "Err::LineEnding";
    const ERR_IS_A = "Err::IsA";

}