<?php

namespace Parcom;

class ErrorKind
{
    private const EOF = 'EOF';
    private const TAG = 'Tag';
    private const TAKE_TILL_1 = 'TakeTill1';
    private const PERMUTATION = 'Permutation';
    private const ALPHA = 'Alpha';
    private const DIGIT = 'Digit';

    private string $variant;

    private function __construct(string $variant)
    {
        $this->variant = $variant;
    }

    public static function Eof(): self
    {
        return new self(self::EOF);
    }

    public static function Tag(): self
    {
        return new self(self::TAG);
    }

    public static function TakeTill1(): self
    {
        return new self(self::TAKE_TILL_1);
    }

    public static function Permutation()
    {
        return new self(self::PERMUTATION);
    }

    public static function Alpha()
    {
        return new self(self::ALPHA);
    }

    public static function Digit()
    {
        return new self(self::DIGIT);
    }

    public function __toString(): string
    {
        return $this->variant;
    }

}