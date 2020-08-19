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
    private const TAKE_WHILE1 = 'TakeWhile1';
    private const TAKE_WHILE_M_N = 'TakeWhileMN';
    private const IS_A = 'IsA';
    private const IS_NOT = 'IsNot';
    private const TAKE_UNTIL = 'TakeUntil';

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

    public static function TakeWhile1()
    {
        return new self(self::TAKE_WHILE1);
    }

    public static function TakeWhileMN()
    {
        return new self(self::TAKE_WHILE_M_N);
    }

    public static function IsA()
    {
        return new self(self::IS_A);
    }

    public static function IsNot()
    {
        return new self(self::IS_NOT);
    }

    public static function TakeUntil()
    {
        return new self(self::TAKE_UNTIL);
    }

    public function __toString(): string
    {
        return $this->variant;
    }

}