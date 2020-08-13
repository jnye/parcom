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

}