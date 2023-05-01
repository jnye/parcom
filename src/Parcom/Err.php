<?php

namespace Parcom;

use InvalidArgumentException;

class Err
{

    public const INCOMPLETE = "Incomplete";
    public const ERROR = "Error";
    public const FAILURE = "Failure";

    private string $variant;
    private ?ErrorKind $errorKind;
    private ?Needed $needed;

    private function __construct(string $variant, ?ErrorKind $errorKind, ?Needed $needed = null)
    {
        $this->variant = $variant;
        $this->errorKind = $errorKind;
        $this->needed = $needed;
    }

    public static function Incomplete(Needed $needed): self
    {
        return new self(self::INCOMPLETE, null, $needed);
    }

    public static function Error(Input $input, ErrorKind $errorKind): self
    {
        return new self(self::ERROR, $errorKind);
    }

    public static function Failure(Input $input, ErrorKind $errorKind): self
    {
        return new self(self::FAILURE, $errorKind);
    }

    public function variant(): string
    {
        return $this->variant;
    }

    public function __toString(): string
    {
        if ($this->variant == self::INCOMPLETE) {
            return (string)$this->needed;
        } else if ($this->variant == self::ERROR) {
            return (string)$this->errorKind;
        } else if ($this->variant == self::FAILURE) {
            return (string)$this->errorKind;
        } else {
            throw new InvalidArgumentException("Unknown Err variant encountered: '{$this->variant}'");
        }
    }

}